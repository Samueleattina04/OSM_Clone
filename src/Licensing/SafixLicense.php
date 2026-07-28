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

namespace Licensing;

use Carbon\Carbon;

/**
 * Gestisce lo stato della licenza e dell'abbonamento di manutenzione Safix
 * per l'installazione corrente, in base a quanto configurato in config.inc.php.
 *
 * Modello commerciale supportato:
 * - licenza una tantum per l'attivazione del gestionale;
 * - abbonamento mensile per la manutenzione/assistenza;
 * - moduli/funzionalità aggiuntive sbloccate singolarmente a pagamento.
 */
class SafixLicense
{
    /** @var array|null */
    protected static $config;

    /**
     * Restituisce la configurazione di licenza, con i valori di default se non impostati.
     *
     * @return array
     */
    public static function getConfig()
    {
        if (self::$config === null) {
            $config = \App::getConfig()['license'] ?? [];

            self::$config = array_merge([
                'licensed_to' => '',
                'active' => false,
                'activated_at' => '',
                'maintenance_active' => false,
                'maintenance_expires_at' => '',
                'modules' => [],
                'support_email' => '',
            ], $config);
        }

        return self::$config;
    }

    /**
     * Indica se la licenza base è stata attivata per questa installazione.
     *
     * @return bool
     */
    public static function isActive()
    {
        return !empty(self::getConfig()['active']);
    }

    /**
     * Indica se l'abbonamento di manutenzione risulta attivo e non scaduto.
     *
     * @return bool
     */
    public static function isMaintenanceActive()
    {
        $config = self::getConfig();

        if (empty($config['maintenance_active'])) {
            return false;
        }

        $expiration = self::maintenanceExpiration();

        return $expiration === null || $expiration->isFuture();
    }

    /**
     * Restituisce la data di scadenza dell'abbonamento di manutenzione, se impostata.
     *
     * @return Carbon|null
     */
    public static function maintenanceExpiration()
    {
        $date = self::getConfig()['maintenance_expires_at'];

        return !empty($date) ? Carbon::parse($date) : null;
    }

    /**
     * Restituisce i giorni rimanenti alla scadenza dell'abbonamento di manutenzione.
     * Un valore negativo indica che l'abbonamento è già scaduto da quel numero di giorni.
     *
     * @return int|null
     */
    public static function maintenanceDaysRemaining()
    {
        $expiration = self::maintenanceExpiration();

        if ($expiration === null) {
            return null;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays($expiration->startOfDay(), false);
    }

    /**
     * Restituisce lo stato sintetico della licenza:
     * "unlicensed", "expired", "expiring" oppure "active".
     *
     * @return string
     */
    public static function status()
    {
        if (!self::isActive()) {
            return 'unlicensed';
        }

        if (!self::isMaintenanceActive()) {
            return 'expired';
        }

        $remaining = self::maintenanceDaysRemaining();

        if ($remaining !== null && $remaining <= 15) {
            return 'expiring';
        }

        return 'active';
    }

    /**
     * Indica se un determinato modulo/funzionalità aggiuntiva è stato sbloccato per il cliente.
     *
     * @param string $module
     *
     * @return bool
     */
    public static function hasModule($module)
    {
        return in_array($module, self::getConfig()['modules'] ?? [], true);
    }
}
