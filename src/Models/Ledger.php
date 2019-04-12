<?php

namespace Scottlaurent\Accounting\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;

/**
 * Class Journal.
 *
 * @property    Money                  $balance
 * @property    string                 $currency
 * @property    Carbon                 $updated_at
 * @property    Carbon                 $post_date
 * @property    Carbon                 $created_at
 */
class Ledger extends Model
{
    /**
     * @var string
     */
    protected $table = 'accounting_ledgers';

    public $currency = 'USD';

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * Get all of the posts for the country.
     */
    public function journal_transctions()
    {
        return $this->hasManyThrough(JournalTransaction::class, Journal::class);
    }

    public function getCurrentBalance()
    {
        if ($this->type == 'asset' || $this->type == 'expense') {
            $balance = $this->journal_transctions->sum('debit') - $this->journal_transctions->sum('credit');
        } else {
            $balance = $this->journal_transctions->sum('credit') - $this->journal_transctions->sum('debit');
        }

        return new Money($balance, new Currency($this->currency));
    }

    public function getCurrentBalanceInDollars()
    {
        return $this->getCurrentBalance()->getAmount() / 100;
    }
}
