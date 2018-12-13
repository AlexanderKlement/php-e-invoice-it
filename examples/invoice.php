<?php

/**
 * Copyright (C) 2018 Taocomp s.r.l.s. <https://taocomp.com>
 *
 * This file is part of php-sdicoop-invoice.
 *
 * php-sdicoop-invoice is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-sdicoop-invoice is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-sdicoop-invoice.  If not, see <http://www.gnu.org/licenses/>.
 */

use \Taocomp\Sdicoop\Invoice;
use \Taocomp\Sdicoop\Notification;

try
{
    require_once(__DIR__ . '/../autoload.php');

    // Add some invoice templates
    Invoice::setTemplate('FPA12', __DIR__ . '/../templates/FPA12.xml');
    Invoice::setTemplate('FPR12', __DIR__ . '/../templates/FPR12.xml');

    // Create a new FPR12 invoice
    $invoice = Invoice::factory('FPR12');

    // Set some invoice data
    $invoice->DatiTrasmissione(array(
        'IdTrasmittente/IdCodice' => '00011122233',
        'IdTrasmittente/IdPaese'  => 'IT',
    ));

    $invoice->CedentePrestatore(array(
        'DatiAnagrafici/IdFiscaleIVA/IdPaese'     => 'IT',
        'DatiAnagrafici/IdFiscaleIVA/IdCodice'    => '00011122233',
        'DatiAnagrafici/Anagrafica/Denominazione' => 'GAMMA',
        'DatiAnagrafici/RegimeFiscale'            => 'RF19',
        'Sede/Indirizzo'                          => 'VIA DEL TAO 3',
        'Sede/CAP'                                => '73100'
    ));

    $invoice->DatiTrasmissione()->ProgressivoInvio = random_int(10000, 99999);
    $invoice->DatiTrasmissione()->CodiceDestinatario = '0000000';

    $invoice->DatiGenerali(array(
        'DatiGeneraliDocumento/Numero' => 98765,
        'DatiGeneraliDocumento/Data'   => date('Y-m-d')
    ));

    // Save invoice
    $invoice->save(__DIR__);
    // or
    // Invoice::setDestinationDir(__DIR__);
    // $invoice->save();

    // Add a notification template
    Notification::setTemplate('EC', __DIR__ . '/../templates/EC.xml');

    // Create a notification from invoice
    $notification = $invoice->prepareNotification('EC');
    // or an empty one:
    // $notification = Notification::factory('EC');

    // Edit data
    $notification->IdentificativoSdI = 1010101;
    $notification->Esito = Notification::EC02;

    // Save to file
    $notificationFile = __DIR__ . '/'
                      . basename($invoice->getFilename(), '.xml')
                      . '_EC_001.xml';
    $notification->save($notificationFile, true);
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
