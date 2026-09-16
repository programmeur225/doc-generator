{{-- $currentStep : 1, 2 ou 3 --}}
<div class="stepper">
    <div class="step-wrap">
        <div class="step-circle {{ $currentStep >= 1 ? 'done' : '' }} {{ $currentStep == 1 ? 'current' : '' }}">
            {{ $currentStep > 1 ? '✓' : '1' }}
        </div>
        <div class="step-label {{ $currentStep == 1 ? 'active' : '' }}">Pays</div>
    </div>
    <div class="step-line {{ $currentStep > 1 ? 'done' : '' }}"></div>
    <div class="step-wrap">
        <div class="step-circle {{ $currentStep >= 2 ? 'done' : '' }} {{ $currentStep == 2 ? 'current' : '' }}">
            {{ $currentStep > 2 ? '✓' : '2' }}
        </div>
        <div class="step-label {{ $currentStep == 2 ? 'active' : '' }}">Document</div>
    </div>
    <div class="step-line {{ $currentStep > 2 ? 'done' : '' }}"></div>
    <div class="step-wrap">
        <div class="step-circle {{ $currentStep >= 3 ? 'done' : '' }} {{ $currentStep == 3 ? 'current' : '' }}">3</div>
        <div class="step-label {{ $currentStep == 3 ? 'active' : '' }}">Formulaire</div>
    </div>
</div>
