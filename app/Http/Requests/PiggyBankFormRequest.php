<?php
/**
 * PiggyBankFormRequest.php
 * Copyright (C) 2016 thegrumpydictator@gmail.com
 *
 * This software may be modified and distributed under the terms of the
 * Creative Commons Attribution-ShareAlike 4.0 International License.
 *
 * See the LICENSE file for details.
 */

declare(strict_types = 1);

namespace FireflyIII\Http\Requests;

use Carbon\Carbon;

/**
 * Class PiggyBankFormRequest
 *
 *
 * @package FireflyIII\Http\Requests
 */
class PiggyBankFormRequest extends Request
{
    /**
     * @return bool
     */
    public function authorize()
    {
        // Only allow logged in users
        return auth()->check();
    }

    /**
     * @return array
     */
    public function getPiggyBankData(): array
    {
        return [
            'name'         => trim($this->get('name')),
            'startdate'    => new Carbon,
            'account_id'   => intval($this->get('account_id')),
            'targetamount' => round($this->get('targetamount'), 12),
            'targetdate'   => strlen(strval($this->get('targetdate'))) > 0 ? new Carbon($this->get('targetdate')) : null,
            'note'         => trim(strval($this->get('note'))),
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {

        $nameRule       = 'required|between:1,255|uniquePiggyBankForUser';
        $targetDateRule = 'date';
        if (intval($this->get('id'))) {
            $nameRule = 'required|between:1,255|uniquePiggyBankForUser:' . intval($this->get('id'));
        }


        $rules = [
            'name'                            => $nameRule,
            'account_id'                      => 'required|belongsToUser:accounts',
            'targetamount'                    => 'required|numeric|more:0',
            'amount_currency_id_targetamount' => 'required|exists:transaction_currencies,id',
            'startdate'                       => 'date',
            'targetdate'                      => $targetDateRule,
            'order'                           => 'integer|min:1',

        ];

        return $rules;
    }
}
