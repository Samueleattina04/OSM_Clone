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

use Modules\Valutazioni\Valutazione;

include_once __DIR__.'/../../core.php';

if (!empty($id_record)) {
    $valutazione = Valutazione::find($id_record);

    if (!empty($valutazione)) {
        $record = $valutazione->toArray();
        $record['link'] = $valutazione->link;
        $record['intervento_codice'] = $valutazione->intervento->codice;
        $record['cliente'] = $valutazione->intervento->anagrafica->ragione_sociale ?? '';
    }
}
