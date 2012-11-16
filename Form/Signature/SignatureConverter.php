<?php

/**
 * Copyright (c) MMXI–, Thomas J Bradley
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * - Neither the name of Thomas J Bradley nor the names of its contributors may
 * be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace TerraMar\Bundle\SalesBundle\Form\Signature;

/**
 * Signature to Image: A supplemental script for Signature Pad that
 * generates an image of the signature’s JSON output server-side using PHP.
 *
 * @project ca.thomasjbradley.applications.signaturetoimage
 * @author Thomas J Bradley <hey@thomasjbradley.ca>
 * @link http://thomasjbradley.ca/lab/signature-to-image
 * @link http://github.com/thomasjbradley/signature-to-image
 * @copyright Copyright MMXI–, Thomas J Bradley
 * @license New BSD License
 * @version 1.1.0
 *
 * Modifications by Tyler Sommer <sommertm@gmail.com> to allow for PSR-0 autoloading and project style rules
 */
class SignatureConverter
{
    /**
     *  Accepts a signature created by signature pad in Json format
     *  Converts it to an image resource
     *  The image resource can then be changed into png, jpg whatever PHP GD supports
     *
     *  To create a nicely anti-aliased graphic the signature is drawn 12 times it's original size then shrunken
     *
     * @param string|array $json
     * @param array $options OPTIONAL; the options for image creation
     *    imageSize => array(width, height)
     *    bgColour => array(red, green, blue) | transparent
     *    penWidth => int
     *    penColour => array(red, green, blue)
     *    drawMultiplier => int
     *
     * @throws \RuntimeException if the json provided is invalid
     * @return object
     */
    public static function convertToImage($json, $options = array())
    {
        $defaultOptions = array(
            'imageSize' => array(198, 55),
            'bgColour' => array(0xff, 0xff, 0xff),
            'penWidth' => 2,
            'penColour' => array(0x14, 0x53, 0x94),
            'drawMultiplier'=> 12
        );

        $options = array_merge($defaultOptions, $options);

        $img = imagecreatetruecolor($options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][1] * $options['drawMultiplier']);

        if ($options['bgColour'] == 'transparent') {
            imagesavealpha($img, true);
            $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
        } else {
            $bg = imagecolorallocate($img, $options['bgColour'][0], $options['bgColour'][1], $options['bgColour'][2]);
        }

        $pen = imagecolorallocate($img, $options['penColour'][0], $options['penColour'][1], $options['penColour'][2]);
        imagefill($img, 0, 0, $bg);

        if (is_string($json)) {
            $json = json_decode(stripslashes($json));
        }

        if (!$json) {
            throw new \RuntimeException('Unable to parse signature data');
        }

        foreach ($json as $v) {
            static::drawThickLine(
                $img,
                $v->lx * $options['drawMultiplier'],
                $v->ly * $options['drawMultiplier'],
                $v->mx * $options['drawMultiplier'],
                $v->my * $options['drawMultiplier'],
                $pen,
                $options['penWidth'] * ($options['drawMultiplier'] / 2)
            );
        }

        $imgDest = imagecreatetruecolor($options['imageSize'][0], $options['imageSize'][1]);

        if ($options['bgColour'] == 'transparent') {
            imagealphablending($imgDest, false);
            imagesavealpha($imgDest, true);
        }

        imagecopyresampled($imgDest, $img, 0, 0, 0, 0, $options['imageSize'][0], $options['imageSize'][0], $options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][0] * $options['drawMultiplier']);
        imagedestroy($img);

        return $imgDest;
    }

    /**
     *  Draws a thick line
     *  Changing the thickness of a line using imagesetthickness doesn't produce as nice of result
     *
     *  @param object $img
     *  @param int $startX
     *  @param int $startY
     *  @param int $endX
     *  @param int $endY
     *  @param object $colour
     *  @param int $thickness
     *
     *  @return void
     */
    protected static function drawThickLine($img, $startX, $startY, $endX, $endY, $colour, $thickness)
    {
        $angle = (atan2(($startY - $endY), ($endX - $startX)));

        $dist_x = $thickness * (sin($angle));
        $dist_y = $thickness * (cos($angle));

        $p1x = ceil(($startX + $dist_x));
        $p1y = ceil(($startY + $dist_y));
        $p2x = ceil(($endX + $dist_x));
        $p2y = ceil(($endY + $dist_y));
        $p3x = ceil(($endX - $dist_x));
        $p3y = ceil(($endY - $dist_y));
        $p4x = ceil(($startX - $dist_x));
        $p4y = ceil(($startY - $dist_y));

        $array = array(0=>$p1x, $p1y, $p2x, $p2y, $p3x, $p3y, $p4x, $p4y);
        imagefilledpolygon($img, $array, (count($array)/2), $colour);
    }
}