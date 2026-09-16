@extends('layouts.app')

@section('title', 'Conditions Générales d\'Utilisation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <h1 class="fw-bold mb-1">Conditions Générales d'Utilisation</h1>
        <p class="text-muted mb-4">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5" style="line-height: 1.7;">

                <p>
                    Les présentes Conditions Générales d'Utilisation (ci-après les «&nbsp;CGU&nbsp;») régissent
                    l'accès et l'utilisation du service de génération de documents proposé sur ce site
                    (ci-après le «&nbsp;Service&nbsp;»), édité par
                    <strong>{{ \App\Models\Setting::get('site_name', 'Juris-Expert') }}</strong>
                    (ci-après l'«&nbsp;Éditeur&nbsp;»).
                </p>
                <p>
                    En accédant au Service ou en l'utilisant, vous acceptez sans réserve les présentes CGU.
                    Si vous n'acceptez pas ces conditions, vous ne devez pas utiliser le Service.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">1. Objet et nature du Service</h5>
                <p>
                    <strong>1.1.</strong> Le Service est un <strong>outil numérique d'aide à la rédaction et à la mise en forme de documents</strong>.
                    Il permet à l'Utilisateur de renseigner des informations et d'obtenir un document généré à partir de modèles (templates) proposés sur la plateforme.
                </p>
                <p>
                    <strong>1.2.</strong> Les documents générés par le Service sont des <strong>brouillons / projets de documents</strong>.
                    Ils n'ont <strong>aucune valeur d'acte authentique, d'acte public, de document administratif officiel ou de titre exécutoire</strong>.
                </p>
                <p>
                    <strong>1.3.</strong> Le Service <strong>ne constitue en aucun cas</strong> :
                </p>
                <ul>
                    <li>un conseil juridique, fiscal, administratif ou professionnel ;</li>
                    <li>un substitut à l'intervention d'un avocat, d'un notaire, d'un huissier, d'une administration ou de toute autorité compétente ;</li>
                    <li>une garantie de conformité du document à la réglementation en vigueur ou à la situation particulière de l'Utilisateur.</li>
                </ul>
                <p>
                    <strong>1.4.</strong> L'Utilisateur reste <strong>seul responsable</strong> de vérifier le contenu, la pertinence et la licéité du document généré, ainsi que d'accomplir, le cas échéant, les formalités officielles nécessaires (signature, légalisation, enregistrement, etc.).
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">2. Accès au Service et compte Utilisateur</h5>
                <p>
                    <strong>2.1.</strong> L'accès à certaines fonctionnalités peut nécessiter la création d'un compte.
                    L'Utilisateur s'engage à fournir des informations exactes et à les maintenir à jour.
                </p>
                <p>
                    <strong>2.2.</strong> L'Utilisateur est responsable de la confidentialité de ses identifiants et de toute activité réalisée depuis son compte.
                </p>
                <p>
                    <strong>2.3.</strong> L'Éditeur se réserve le droit de suspendre ou de supprimer un compte en cas de manquement aux présentes CGU, de suspicion d'usage frauduleux, ou pour tout motif légitime de sécurité ou de conformité.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">3. Obligations et responsabilités de l'Utilisateur</h5>
                <p>
                    <strong>3.1.</strong> L'Utilisateur s'engage à utiliser le Service de manière <strong>loyale, licite et conforme</strong> aux lois en vigueur en Côte d'Ivoire, notamment au Code pénal.
                </p>
                <p>
                    <strong>3.2.</strong> L'Utilisateur est seul responsable :
                </p>
                <ul>
                    <li>des informations qu'il saisit dans le Service ;</li>
                    <li>du contenu des documents qu'il génère ;</li>
                    <li>de l'usage qu'il fait de ces documents ;</li>
                    <li>des conséquences de cet usage à l'égard de tiers ou des administrations.</li>
                </ul>
                <p>
                    <strong>3.3.</strong> <strong>Il est strictement interdit</strong> d'utiliser le Service, directement ou indirectement, pour :
                </p>
                <ul>
                    <li>
                        <strong>a)</strong> fabriquer, reproduire, imiter, falsifier ou altérer des documents administratifs ou officiels,
                        ou des documents en tenant lieu (notamment, sans que cette liste soit exhaustive :
                        cartes d'identité, passeports, extraits d'actes de l'état civil, certificats de nationalité,
                        casiers judiciaires, diplômes d'État, titres fonciers, permis, autorisations administratives, etc.) ;
                    </li>
                    <li>
                        <strong>b)</strong> établir sciemment des attestations ou certificats faisant état de faits matériellement inexacts ;
                    </li>
                    <li>
                        <strong>c)</strong> faciliter, préparer ou commettre un faux en écriture publique, privée, de commerce ou de banque,
                        ou un usage de faux, au sens des dispositions du Code pénal ivoirien
                        (notamment les articles 308, 309, 311, 312, 479 et 481 de la loi n° 2019-574 portant Code pénal) ;
                    </li>
                    <li>
                        <strong>d)</strong> tromper des tiers, des administrations ou toute autorité sur l'identité, les droits, les qualités ou la situation d'une personne ;
                    </li>
                    <li>
                        <strong>e)</strong> contrefaire des sceaux, timbres, marques, en-têtes ou signes distinctifs d'une administration publique ou d'une personne morale.
                    </li>
                </ul>
                <p>
                    <strong>3.4.</strong> Tout manquement aux interdictions ci-dessus pourra entraîner, sans préavis :
                    la suspension ou la résiliation immédiate du compte ;
                    le signalement aux autorités compétentes ;
                    l'engagement de poursuites judiciaires, sans préjudice de tous dommages et intérêts.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">4. Documents générés – Avertissement</h5>
                <p>
                    <strong>4.1.</strong> Chaque document généré par le Service porte, ou est réputé porter, la mention suivante (ou une mention équivalente) :
                </p>
                <div class="alert alert-secondary text-center my-3">
                    <strong>BROUILLON – SANS VALEUR OFFICIELLE</strong><br>
                    Document généré par un outil d'aide à la rédaction.<br>
                    Ne constitue pas un acte authentique ni un document administratif.
                </div>
                <p>
                    <strong>4.2.</strong> L'Utilisateur s'interdit de retirer, masquer ou altérer cette mention dans le but de faire passer le document pour un acte officiel ou authentique.
                </p>
                <p>
                    <strong>4.3.</strong> L'Éditeur ne garantit ni l'exactitude, ni l'exhaustivité, ni l'adéquation des modèles proposés à une situation particulière, ni la conformité du document final aux exigences d'une administration ou d'un tiers.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">5. Propriété intellectuelle</h5>
                <p>
                    <strong>5.1.</strong> L'ensemble des éléments du Service (logiciel, interface, modèles, textes, graphismes, logos, etc.) est protégé par le droit de la propriété intellectuelle et reste la propriété exclusive de l'Éditeur ou de ses partenaires.
                </p>
                <p>
                    <strong>5.2.</strong> L'Utilisateur conserve les droits sur le contenu qu'il saisit.
                    En utilisant le Service, il concède à l'Éditeur une licence non exclusive, gratuite et mondiale, uniquement dans la mesure nécessaire au fonctionnement du Service (génération, stockage temporaire, affichage).
                </p>
                <p>
                    <strong>5.3.</strong> Toute reproduction ou exploitation non autorisée des éléments du Service est interdite.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">6. Données personnelles</h5>
                <p>
                    <strong>6.1.</strong> Le traitement des données personnelles est décrit dans la Politique de confidentialité accessible sur le site (lorsqu'elle est publiée).
                </p>
                <p>
                    <strong>6.2.</strong> L'Utilisateur s'engage à ne saisir dans le Service que des données dont il a le droit de disposer et à respecter la réglementation applicable en matière de protection des données.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">7. Disponibilité et évolution du Service</h5>
                <p>
                    <strong>7.1.</strong> L'Éditeur s'efforce d'assurer la disponibilité du Service, sans garantir un accès ininterrompu ou exempt d'erreurs.
                </p>
                <p>
                    <strong>7.2.</strong> L'Éditeur peut à tout moment modifier, suspendre ou interrompre tout ou partie du Service, notamment pour maintenance, sécurité, évolution ou mise en conformité.
                </p>
                <p>
                    <strong>7.3.</strong> L'Éditeur se réserve le droit de modifier les modèles disponibles, d'en retirer, ou de refuser la génération de certains types de documents.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">8. Limitation de responsabilité</h5>
                <p>
                    <strong>8.1.</strong> Dans les limites autorisées par la loi ivoirienne, l'Éditeur ne saurait être tenu responsable :
                </p>
                <ul>
                    <li>de l'usage illicite ou frauduleux du Service ou des documents générés par l'Utilisateur ou un tiers ;</li>
                    <li>des dommages résultant d'informations inexactes, incomplètes ou illicites saisies par l'Utilisateur ;</li>
                    <li>de l'absence de valeur officielle ou juridique des documents générés ;</li>
                    <li>des décisions prises par l'Utilisateur ou des tiers sur la base de ces documents ;</li>
                    <li>des interruptions, bugs, pertes de données ou dommages indirects (perte de chance, préjudice commercial, etc.).</li>
                </ul>
                <p>
                    <strong>8.2.</strong> La responsabilité de l'Éditeur, lorsqu'elle est engagée, est limitée aux dommages directs et prévisibles,
                    et plafonnée au montant total payé par l'Utilisateur au titre du Service au cours des douze (12) derniers mois
                    (ou à zéro franc CFA si aucun paiement n'a été effectué).
                </p>
                <p>
                    <strong>8.3.</strong> Aucune disposition des présentes CGU n'exclut ou ne limite la responsabilité en cas de dol, de faute lourde,
                    ou dans les cas où la loi interdit une telle limitation.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">9. Suspension et résiliation</h5>
                <p>
                    <strong>9.1.</strong> L'Utilisateur peut cesser d'utiliser le Service à tout moment et demander la suppression de son compte selon les modalités prévues sur le site.
                </p>
                <p>
                    <strong>9.2.</strong> L'Éditeur peut suspendre ou résilier l'accès au Service, immédiatement et sans indemnité, en cas de :
                </p>
                <ul>
                    <li>manquement aux présentes CGU ;</li>
                    <li>suspicion d'usage frauduleux ou illicite ;</li>
                    <li>demande d'une autorité compétente ;</li>
                    <li>comportement portant atteinte à la sécurité ou à la réputation du Service.</li>
                </ul>

                <hr class="my-4">

                <h5 class="fw-bold">10. Modifications des CGU</h5>
                <p>
                    L'Éditeur peut modifier les présentes CGU à tout moment.
                    La version applicable est celle publiée sur le site à la date d'utilisation du Service.
                    En cas de modification substantielle, l'Utilisateur en sera informé par tout moyen utile.
                    La poursuite de l'utilisation du Service après modification vaut acceptation des nouvelles CGU.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">11. Droit applicable et litiges</h5>
                <p>
                    <strong>11.1.</strong> Les présentes CGU sont régies par le <strong>droit ivoirien</strong>.
                </p>
                <p>
                    <strong>11.2.</strong> En cas de litige relatif à l'interprétation ou à l'exécution des présentes CGU, les parties s'efforceront de trouver une solution amiable.
                </p>
                <p>
                    <strong>11.3.</strong> À défaut d'accord amiable dans un délai de trente (30) jours, le litige sera soumis aux
                    <strong>juridictions compétentes d'Abidjan</strong>, sous réserve des règles d'ordre public applicables.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">12. Contact</h5>
                <p>
                    Pour toute question relative aux présentes CGU ou au Service :<br>
                    <strong>{{ \App\Models\Setting::get('support_email', 'contact@example.com') }}</strong>
                </p>

                <hr class="my-4">

                <h5 class="fw-bold">13. Dispositions diverses</h5>
                <p>
                    <strong>13.1.</strong> Si une clause des présentes CGU est déclarée nulle ou inapplicable, les autres clauses restent en vigueur.
                </p>
                <p>
                    <strong>13.2.</strong> Le fait pour l'Éditeur de ne pas se prévaloir d'un manquement ne vaut pas renonciation à se prévaloir ultérieurement d'un tel manquement.
                </p>
                <p>
                    <strong>13.3.</strong> Les présentes CGU constituent l'intégralité de l'accord entre l'Utilisateur et l'Éditeur concernant l'utilisation du Service, sous réserve des conditions particulières éventuelles (offre payante, etc.).
                </p>

                <div class="alert alert-light border mt-5 mb-0">
                    <strong>En utilisant le Service, vous reconnaissez avoir lu, compris et accepté les présentes Conditions Générales d'Utilisation.</strong>
                </div>

            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('generate.countries') }}" class="btn btn-outline-secondary">← Retour</a>
        </div>
    </div>
</div>
@endsection
