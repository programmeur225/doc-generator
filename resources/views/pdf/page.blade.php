<!DOCTYPE html>
<html>
<head>
<style>
    @page { margin: 0; }
    body { margin: 0; padding: 0; }
    @if($customFontRegular)
    @font-face {
        font-family: 'CustomFont';
        src: url('{{ $customFontRegular }}');
        font-weight: 400;
        font-style: normal;
    }
    @if($customFontBold)
    @font-face {
        font-family: 'CustomFont';
        src: url('{{ $customFontBold }}');
        font-weight: 700;
        font-style: normal;
    }
    @endif
    @endif
    .page {
        position: relative;
        width: {{ $widthPt }}pt;
        height: {{ $heightPt }}pt;
    }
    .page img {
        position: absolute;
        top: 0;
        left: 0;
        width: {{ $widthPt }}pt;
        height: {{ $heightPt }}pt;
    }
    .field-bg {
        position: absolute;
        overflow: hidden;
    }
    .field {
        position: absolute;
        overflow: hidden;
        white-space: pre-wrap;
        word-break: break-word;
        overflow-wrap: anywhere;
        box-sizing: border-box;
        text-rendering: optimizeLegibility;
        font-kerning: normal;
        font-synthesis-weight: none;
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
        line-height: 0.95;
        letter-spacing: 0;
        padding: 0 0.6pt 0.2pt 0.6pt;
    }
</style>
</head>
<body>
    <div class="page">
        <img src="{{ $imagePath }}">

        @foreach ($variables as $variable)
            @php
                $value = $data[$variable->key] ?? '';
                if ($variable->type === 'checkbox') {
                    $value = $value ? '☑' : '☐';
                }

                $left = ($variable->position_x / 100) * $widthPt;
                $top = ($variable->position_y / 100) * $heightPt;
                $boxWidth = ($variable->box_width / 100) * $widthPt;
                $boxHeight = ($variable->box_height / 100) * $heightPt;
                $bgColor = $variable->background_color ?? '#FFFFFF';

                $text = trim((string) $value);
                $normalizedText = preg_replace('/\s+/', ' ', $text);
                $charCount = mb_strlen($normalizedText, 'UTF-8');

                $fontFamily = $customFontRegular ? 'CustomFont' : ($variable->font_family ?? 'DejaVu Sans');
                $fontWeight = preg_match('/\b(bold|heavy|black|semibold|demi)\b/i', (string) $fontFamily)
                    ? 700
                    : 400;

                $estimateTextMetrics = function ($input, $availableWidth, $fontSize) {
                    if (trim((string) $input) === '') {
                        return ['width' => 0, 'lines' => 1];
                    }

                    $width = max(1.0, (float) $availableWidth);
                    $size = max(4.0, (float) $fontSize);
                    $lines = 1;
                    $currentLineWidth = 0.0;
                    $maxLineWidth = 0.0;

                    $tokens = preg_split('/\s+/', trim((string) $input));

                    foreach ($tokens as $token) {
                        $tokenWidth = 0.0;
                        $chars = preg_split('//u', $token, -1, PREG_SPLIT_NO_EMPTY);

                        foreach ($chars as $char) {
                            if ($char === '-' || $char === '/' || $char === ':' || $char === '.') {
                                $tokenWidth += $size * 0.35;
                                continue;
                            }

                            if (preg_match('/[A-Z0-9]/u', $char)) {
                                $tokenWidth += $size * 0.62;
                            } elseif (preg_match('/[a-z]/u', $char)) {
                                $tokenWidth += $size * 0.55;
                            } else {
                                $tokenWidth += $size * 0.58;
                            }
                        }

                        $tokenWidth += $size * 0.35;

                        if ($currentLineWidth > 0 && ($currentLineWidth + $tokenWidth) > $width) {
                            $lines++;
                            $currentLineWidth = $tokenWidth;
                        } else {
                            $currentLineWidth += $tokenWidth;
                        }

                        $maxLineWidth = max($maxLineWidth, $currentLineWidth);
                    }

                    return ['width' => $maxLineWidth, 'lines' => max(1, (int) $lines)];
                };

                $auto = $variable->auto_font_size ?? true;
                if ($auto) {
                    $safeWidth = max(12, $boxWidth);
                    $safeHeight = max(12, $boxHeight);
                    $minFontSize = 5.0;
                    $maxFontSize = 72.0;
                    $bestFontSize = $minFontSize;
                    $low = $minFontSize;
                    $high = $maxFontSize;

                    while ($low <= $high) {
                        $mid = ($low + $high) / 2.0;
                        $metrics = $estimateTextMetrics($normalizedText, $safeWidth, $mid);
                        $lineHeight = $mid * 1.12;
                        $estimatedHeight = $metrics['lines'] * $lineHeight;
                        $fitsHeight = $estimatedHeight <= ($safeHeight * 1.05);
                        $fitsWidth = $metrics['width'] <= ($safeWidth * 1.08);

                        if ($fitsHeight && $fitsWidth) {
                            $bestFontSize = $mid;
                            $low = $mid + 0.5;
                        } else {
                            $high = $mid - 0.5;
                        }
                    }

                    $fontSize = max(6.0, min(72.0, round($bestFontSize, 1)));

                    if ($charCount === 0) {
                        $fontSize = max(6.0, min(72.0, $safeHeight * 0.62));
                    }
                } else {
                    $fontSize = (float) ($variable->font_size ?? 12);
                }

                $align = $variable->text_align ?? 'left';
            @endphp

            <div class="field-bg" style="
                left: {{ $left }}pt;
                top: {{ $top }}pt;
                width: {{ $boxWidth }}pt;
                height: {{ $boxHeight }}pt;
                background-color: {{ $bgColor }};
            "></div>

            <div class="field" style="
                left: {{ $left }}pt;
                top: {{ $top }}pt;
                width: {{ $boxWidth }}pt;
                height: {{ $boxHeight }}pt;
                font-family: '{{ $fontFamily }}', sans-serif;
                font-size: {{ round($fontSize, 1) }}pt;
                font-weight: {{ $fontWeight }};
                color: {{ $variable->font_color }};
                text-align: {{ $align }};
                justify-content: {{ $align === 'right' ? 'flex-end' : ($align === 'center' ? 'center' : 'flex-start') }};
            ">{{ $value }}</div>
        @endforeach
    </div>
</body>
</html>
