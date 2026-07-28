<?php

/*
 * Safix: gestionale per l'assistenza tecnica e la fatturazione elettronica
 * Copyright (C) DevCode s.r.l. (base OpenSTAManager, GPL-3.0-or-later)
 * Modifiche per il rebranding Safix: Copyright (C) Samuele Attinà
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

// Questo file sostituisce il contenuto predefinito della pagina "Informazioni" (info.php),
// che viene mostrato automaticamente solo quando assistenza.php non esiste.

use Licensing\SafixLicense;

$license = SafixLicense::getConfig();
$status = SafixLicense::status();
$giorni_manutenzione = SafixLicense::maintenanceDaysRemaining();

$status_labels = [
    'unlicensed' => ['label' => tr('Non attivata'), 'class' => 'status-unlicensed', 'icon' => 'fa-times-circle'],
    'expired' => ['label' => tr('Manutenzione scaduta'), 'class' => 'status-expired', 'icon' => 'fa-exclamation-triangle'],
    'expiring' => ['label' => tr('In scadenza'), 'class' => 'status-expiring', 'icon' => 'fa-clock-o'],
    'active' => ['label' => tr('Attiva'), 'class' => 'status-active', 'icon' => 'fa-check-circle'],
];
$current_status = $status_labels[$status];

echo '
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <div class="card-title"><i class="fa fa-key mr-2"></i>'.tr('Licenza e abbonamento Safix').'</div>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>'.tr('Intestatario licenza').':</strong> '.(!empty($license['licensed_to']) ? htmlspecialchars($license['licensed_to']) : tr('Non specificato')).'</p>
                <p class="mb-2"><strong>'.tr('Stato').':</strong> <span class="safix-license-badge '.$current_status['class'].'"><i class="fa '.$current_status['icon'].' mr-1"></i>'.$current_status['label'].'</span></p>';

if (!empty($license['activated_at'])) {
    echo '
                <p class="mb-2"><strong>'.tr('Data attivazione').':</strong> '.htmlspecialchars($license['activated_at']).'</p>';
}

if (SafixLicense::isActive() && !empty($license['maintenance_expires_at'])) {
    echo '
                <p class="mb-2"><strong>'.tr('Scadenza abbonamento manutenzione').':</strong> '.htmlspecialchars($license['maintenance_expires_at']);
    if ($giorni_manutenzione !== null) {
        echo ' <small class="text-muted">('.($giorni_manutenzione >= 0 ? tr('tra _NUM_ giorni', ['_NUM_' => $giorni_manutenzione]) : tr('scaduto da _NUM_ giorni', ['_NUM_' => abs($giorni_manutenzione)])).')</small>';
    }
    echo '</p>';
}

if (!empty($license['modules'])) {
    echo '
                <p class="mb-1"><strong>'.tr('Funzionalità aggiuntive sbloccate').':</strong></p>
                <ul class="mb-2">';
    foreach ($license['modules'] as $module) {
        echo '<li>'.htmlspecialchars($module).'</li>';
    }
    echo '</ul>';
}

echo '
                <p class="text-muted mb-0"><small>'.tr('Il pacchetto base è acquistato con pagamento una tantum. L\'abbonamento mensile copre manutenzione e assistenza; nuove funzionalità o sezioni personalizzate vengono sviluppate e fatturate separatamente su richiesta').'.</small></p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <div class="card-title"><i class="fa fa-life-ring mr-2"></i>'.tr('Assistenza').'</div>
            </div>
            <div class="card-body">
                <p>'.tr('Per rinnovi, richieste di supporto o nuove funzionalità contattaci').':</p>';

if (!empty($license['support_email'])) {
    echo '<p><a href="mailto:'.htmlspecialchars($license['support_email']).'" class="btn btn-info btn-block"><i class="fa fa-envelope mr-1"></i>'.htmlspecialchars($license['support_email']).'</a></p>';
}

echo '
                <p class="mb-0"><strong>'.tr('Versione').':</strong> '.$version.' <small class="text-secondary">('.(!empty($revision) ? 'R'.$revision : tr('In sviluppo')).')</small></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-default card-outline">
            <div class="card-header">
                <div class="card-title"><i class="fa fa-info-circle mr-2"></i>'.tr('Informazioni sul software').'</div>
            </div>
            <div class="card-body">
                <p>'.tr('<b>Safix</b> è distribuito da Safix ed è basato su OpenSTAManager, software libero rilasciato con licenza GPL-3.0 e originariamente sviluppato da DevCode s.r.l.').'</p>
                <p class="mb-0"><strong>'.tr('Licenza open-source').':</strong> <a href="https://www.gnu.org/licenses/gpl-3.0.txt" target="_blank" title="'.tr('Vai al sito per leggere la licenza').'">GPLv3</a></p>
            </div>
        </div>
    </div>
</div>';
