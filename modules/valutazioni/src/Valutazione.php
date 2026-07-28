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

namespace Modules\Valutazioni;

use Common\SimpleModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Interventi\Intervento;

class Valutazione extends Model
{
    use SimpleModelTrait;
    use SoftDeletes;

    protected $table = 'in_valutazioni';

    /**
     * Crea una nuova richiesta di valutazione per l'intervento indicato.
     *
     * @return self
     */
    public static function richiedi(Intervento $intervento)
    {
        $model = new static();
        $model->intervento()->associate($intervento);
        $model->token = bin2hex(random_bytes(20));
        $model->richiesta_at = now();
        $model->save();

        return $model;
    }

    public function intervento()
    {
        return $this->belongsTo(Intervento::class, 'id_intervento');
    }

    public function isRisposta()
    {
        return !empty($this->risposta_at);
    }

    /**
     * URL pubblico che il cliente utilizza per lasciare la valutazione.
     *
     * @return string
     */
    public function getLinkAttribute()
    {
        return BASEURL.'/valutazione.php?token='.$this->token;
    }
}
