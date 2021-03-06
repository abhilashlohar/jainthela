<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PurchaseBooking Entity
 *
 * @property int $id
 * @property int $grn_id
 * @property int $voucher_no
 * @property \Cake\I18n\FrozenDate $transaction_date
 * @property int $vendor_id
 * @property int $jain_thela_admin_id
 * @property \Cake\I18n\FrozenTime $created_on
 *
 * @property \App\Model\Entity\Grn $grn
 * @property \App\Model\Entity\Vendor $vendor
 * @property \App\Model\Entity\JainThelaAdmin $jain_thela_admin
 * @property \App\Model\Entity\PurchaseBookingDetail[] $purchase_booking_details
 */
class PurchaseBooking extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
