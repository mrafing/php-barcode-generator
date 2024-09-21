<?php

namespace Picqer\Barcode\Renderers;

use Picqer\Barcode\Barcode;
use Picqer\Barcode\BarcodeBar;
use Picqer\Barcode\Exceptions\InvalidOptionException;

class SvgRenderer
{
    protected string $foregroundColor = 'black';
    protected string $svgType = self::TYPE_SVG_STANDALONE;

    public const TYPE_SVG_STANDALONE = 'standalone';
    public const TYPE_SVG_INLINE = 'inline';

    public function render(Barcode $barcode, float $width = 200, float $height = 30): string
    {
        $widthFactor = $width / $barcode->getWidth();

        $svg = '';
        if ($this->svgType === self::TYPE_SVG_STANDALONE) {
            $svg .= '<?xml version="1.0" standalone="no" ?>' . PHP_EOL;
            $svg .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . PHP_EOL;
        }
        $svg .= '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" version="1.1" xmlns="http://www.w3.org/2000/svg">' . PHP_EOL;
        $svg .= "\t" . '<desc>' . htmlspecialchars($barcode->getBarcode()) . '</desc>' . PHP_EOL;
        $svg .= "\t" . '<g id="bars" fill="' . $this->foregroundColor . '" stroke="none">' . PHP_EOL;

        // print bars
        $positionHorizontal = 0;
        /** @var BarcodeBar $bar */
        foreach ($barcode->getBars() as $bar) {
            $barWidth = round(($bar->getWidth() * $widthFactor), 3);
            $barHeight = round(($bar->getHeight() * $height / $barcode->getHeight()), 3);

            if ($bar->isBar() && $barWidth > 0) {
                $positionVertical = round(($bar->getPositionVertical() * $height / $barcode->getHeight()), 3);
                // draw a vertical bar
                $svg .= "\t\t" . '<rect x="' . $positionHorizontal . '" y="' . $positionVertical . '" width="' . $barWidth . '" height="' . $barHeight . '" />' . PHP_EOL;
            }

            $positionHorizontal += $barWidth;
        }

        $svg .= "\t</g>" . PHP_EOL;
        $svg .= '</svg>' . PHP_EOL;

        return $svg;
    }

    public function setForegroundColor(string $color): self
    {
        $this->foregroundColor = $color;
        return $this;
    }

    public function setSvgType(string $svgType): self
    {
        if (! in_array($svgType, [self::TYPE_SVG_INLINE, self::TYPE_SVG_STANDALONE])) {
            throw new InvalidOptionException();
        }

        $this->svgType = $svgType;
        return $this;
    }
}