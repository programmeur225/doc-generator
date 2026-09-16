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
        line-height: 0.95;
        letter-spacing: 0;
        text-rendering: optimizeLegibility;
        font-kerning: normal;
        font-synthesis-weight: none;
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
                $fontFamily = $customFontRegular ? 'CustomFont' : ($variable->font_family ?? 'DejaVu Sans');
                $fontWeight = preg_match('/\b(bold|heavy|black|semibold|demi)\b/i', (string) $fontFamily)
                    ? 700
                    : 400;

                $text = trim((string) $value);
                $normalizedText = preg_replace('/\s+/', ' ', $text);
                $charCount = mb_strlen((string) $normalizedText, 'UTF-8');

                $auto = $variable->auto_font_size ?? true;
                if ($auto) {
                    $safeBoxWidth = max($boxWidth, 1);
                    $safeBoxHeight = max($boxHeight, 1);
                    $baseFontSize = min($safeBoxHeight * 0.72, $safeBoxWidth * 0.22);

                    $estimatedCharsPerLine = max(1, (int) floor($safeBoxWidth / max(4, $baseFontSize * 0.58)));
                    $estimatedLines = max(1, (int) ceil($charCount / max(1, $estimatedCharsPerLine)));

                    $fitByHeight = $safeBoxHeight / max(1, $estimatedLines * 1.18);
                    $fitByWidth = $safeBoxWidth / max(1, $charCount * 0.58);
                    $fontSize = max(6, min(72, min($fitByHeight, $fitByWidth, $baseFontSize)));

                    if ($charCount === 0) {
                        $fontSize = max(6, min(72, $safeBoxHeight * 0.62));
                    }
                } else {
                    $fontSize = (float) ($variable->font_size ?? 12);
                }
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
                text-align: {{ $variable->text_align ?? 'left' }};
                padding: 0 0.5pt;
            ">{{ $value }}</div>
        @endforeach
    </div>
</body>
</html>
