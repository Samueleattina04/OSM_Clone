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

include_once __DIR__.'/../../core.php';

$risposto = !empty($record['risposta_at']);
$voto = intval($record['voto']);

?>
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo tr('Valutazione cliente'); ?></h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p class="mb-1"><strong><?php echo tr('Intervento'); ?>:</strong> <?php echo Modules::link('Interventi', $record['id_intervento'], null, $record['intervento_codice']); ?></p>
            </div>
            <div class="col-md-4">
                <p class="mb-1"><strong><?php echo tr('Cliente'); ?>:</strong> <?php echo htmlspecialchars($record['cliente']); ?></p>
            </div>
            <div class="col-md-4">
                <p class="mb-1"><strong><?php echo tr('Stato'); ?>:</strong>
                    <?php if ($risposto) : ?>
                        <span class="badge badge-success"><?php echo tr('Risposto'); ?></span>
                    <?php else : ?>
                        <span class="badge badge-secondary"><?php echo tr('In attesa di risposta'); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <hr>

        <?php if ($risposto) : ?>
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1"><strong><?php echo tr('Voto'); ?>:</strong>
                        <span class="text-warning">
                            <?php for ($i = 1; $i <= 5; ++$i) : ?>
                                <i class="fa fa-star<?php echo $i > $voto ? '-o' : ''; ?>"></i>
                            <?php endfor; ?>
                        </span>
                        (<?php echo $voto; ?>/5)
                    </p>
                </div>
                <div class="col-md-8">
                    <p class="mb-1"><strong><?php echo tr('Data risposta'); ?>:</strong> <?php echo !empty($record['risposta_at']) ? dateFormat($record['risposta_at']) : '-'; ?></p>
                </div>
            </div>

            <?php if (!empty($record['commento'])) : ?>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong><?php echo tr('Commento'); ?>:</strong>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($record['commento'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="alert alert-info mb-0">
                <p class="mb-2"><?php echo tr('Il cliente non ha ancora risposto. Puoi condividere nuovamente il link seguente'); ?>:</p>
                <div class="input-group">
                    <input type="text" class="form-control" id="valutazione-link" value="<?php echo htmlspecialchars($record['link']); ?>" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-info" type="button" onclick="navigator.clipboard.writeText(document.getElementById('valutazione-link').value); this.innerHTML='<i class=\'fa fa-check\'></i>';">
                            <i class="fa fa-copy"></i> <?php echo tr('Copia link'); ?>
                        </button>
                    </div>
                </div>
                <p class="text-muted mt-2 mb-0"><small><?php echo tr('Richiesta inviata il'); ?> <?php echo !empty($record['richiesta_at']) ? dateFormat($record['richiesta_at']) : '-'; ?></small></p>
            </div>
        <?php endif; ?>
    </div>
</div>
