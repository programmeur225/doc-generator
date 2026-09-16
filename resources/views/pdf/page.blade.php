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
        font-weight: normal;
    }
    @if($customFontBold)
    @font-face {
        font-family: 'CustomFont';
        src: url('{{ $customFontBold }}');
        font-weight: bold;
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
    /* Rectangle de fond opaque qui masque le texte original du template */
    .field-bg {
        position: absolute;
        overflow: hidden;
    }
    .field {
        position: absolute;
        overflow: hidden;
        white-space: pre-wrap;
        /* petit padding interne pour que le texte ne colle pas aux bords */
        padding: 1pt 2pt;
        box-sizing: border-box;
        font-family: '{{ $customFontRegular ? 'CustomFont' : ($variable->font_family ?? 'DejaVu Sans') }}', sans-serif;
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

                // Taille de police
                // auto_font_size = true → calcule à partir de la hauteur de la zone
                // (facteur 0.62 pour laisser un peu de padding haut/bas)
                $auto = $variable->auto_font_size ?? true;
                if ($auto) {
                    $computed = $boxHeight * 0.62;
                    // garde des bornes raisonnables
                    $fontSize = max(6, min(72, $computed));
                    // si le texte est très long, on réduit un peu pour qu'il tienne en largeur
                    $len = mb_strlen((string) $value);
                    if ($len > 0 && $boxWidth > 0) {
                        // estimation grossière : ~0.55 * fontSize de largeur par caractère
                        $estWidth = $len * $fontSize * 0.55;
                        if ($estWidth > $boxWidth * 1.15) {
                            $fontSize = max(6, $boxWidth / ($len * 0.55));
                        }
                    }
                } else {
                    $fontSize = $variable->font_size ?? 12;
                }
            @endphp

            {{-- 1. Rectangle opaque qui cache le texte original du template --}}
            <div class="field-bg" style="
                left: {{ $left }}pt;
                top: {{ $top }}pt;
                width: {{ $boxWidth }}pt;
                height: {{ $boxHeight }}pt;
                background-color: {{ $bgColor }};
            "></div>

            {{-- 2. Texte de la variable par-dessus --}}
            <div class="field" style="
                left: {{ $left }}pt;
                top: {{ $top }}pt;
                width: {{ $boxWidth }}pt;
                height: {{ $boxHeight }}pt;
                font-family: '{{ $variable->font_family ?? 'DejaVu Sans' }}', sans-serif;
                font-size: {{ round($fontSize, 1) }}pt;
                color: {{ $variable->font_color }};
                text-align: {{ $variable->text_align }};
            ">{{ $value }}</div>
        @endforeach
    </div>
</body>
</html>
