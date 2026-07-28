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

// Pagina pubblica (nessun login richiesto) che il cliente utilizza per lasciare
// una valutazione al termine di un intervento, tramite link con token univoco.

$skip_permissions = true;
include_once __DIR__.'/core.php';

use Modules\Valutazioni\Valutazione;

$token = get('token');
$valutazione = !empty($token) ? Valutazione::where('token', $token)->first() : null;

if (post('op') == 'valuta' && !empty($valutazione) && !$valutazione->isRisposta()) {
    $voto = (int) post('voto');

    if ($voto >= 1 && $voto <= 5) {
        $valutazione->voto = $voto;
        $valutazione->commento = post('commento');
        $valutazione->risposta_at = now();
        $valutazione->save();
    } else {
        flash()->error(tr('Seleziona un voto da 1 a 5 stelle'));
    }

    redirect_url(base_path_osm().'/valutazione.php?token='.$token);
    exit;
}

$pageTitle = tr('Valutazione del servizio');

include_once App::filepath('include|custom|', 'top.php');

echo '
<style>
    .valutazione-stelle {
        font-size: 2.5rem;
        color: #ccc;
        cursor: pointer;
        direction: rtl;
        display: inline-block;
        unicode-bidi: bidi-override;
    }
    .valutazione-stelle i {
        padding: 0 4px;
        transition: color 0.15s ease;
    }
    .valutazione-stelle input {
        display: none;
    }
    .valutazione-stelle label {
        color: #ccc;
        cursor: pointer;
    }
    .valutazione-stelle input:checked ~ label,
    .valutazione-stelle label:hover,
    .valutazione-stelle label:hover ~ label {
        color: #ffc107;
    }
</style>

<div class="card-center-large" style="max-width: 500px; margin: 5% auto;">
    <div class="card card-outline card-primary shadow-lg">
        <div class="card-header text-center bg-light py-4">
            <img src="'.App::getPaths()['img'].'/logo_completo.png" style="max-width: 220px;" alt="Safix">
        </div>
        <div class="card-body text-center">';

if (empty($valutazione)) {
    echo '
            <p class="lead">'.tr('Link non valido o scaduto').'.</p>';
} elseif ($valutazione->isRisposta()) {
    echo '
            <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
            <p class="lead">'.tr('Grazie, la tua valutazione è già stata registrata').'!</p>';
} else {
    echo '
            <p class="lead mb-4">'.tr('Come valuti il servizio ricevuto durante l\'intervento _CODICE_?', [
        '_CODICE_' => '<strong>'.htmlspecialchars($valutazione->intervento->codice).'</strong>',
    ]).'</p>

            <form action="" method="post">
                <input type="hidden" name="op" value="valuta">

                <div class="valutazione-stelle mb-3">
                    <input type="radio" id="stella5" name="voto" value="5"><label for="stella5" title="5"><i class="fa fa-star"></i></label>
                    <input type="radio" id="stella4" name="voto" value="4"><label for="stella4" title="4"><i class="fa fa-star"></i></label>
                    <input type="radio" id="stella3" name="voto" value="3"><label for="stella3" title="3"><i class="fa fa-star"></i></label>
                    <input type="radio" id="stella2" name="voto" value="2"><label for="stella2" title="2"><i class="fa fa-star"></i></label>
                    <input type="radio" id="stella1" name="voto" value="1"><label for="stella1" title="1"><i class="fa fa-star"></i></label>
                </div>

                <div class="form-group text-left">
                    <label>'.tr('Commento (facoltativo)').'</label>
                    <textarea class="form-control" name="commento" rows="3" placeholder="'.tr('Raccontaci la tua esperienza').'"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block mt-3">'.tr('Invia valutazione').'</button>
            </form>';
}

echo '
        </div>
    </div>
</div>';

include_once App::filepath('include|custom|', 'bottom.php');
