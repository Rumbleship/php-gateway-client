<?php
namespace Rumbleship;

use Rumbleship\Api;
use Exception;


class Gateway extends Api {
    protected $name;

    public function __construct ( $host, $request_options = array())
    {
        parent::__construct($host, $request_options);
        $this->name = 'Rumbleship Gateway';
        $this->description = 'Endpoint SDK for using the gateway';
    }


    /**
     * method to let us know we're ready to use it
     * if not ready we need to try logging in.
     * if that attempt already was made, we might not have correct credentials
     *
     * @return boolean
     */
    public function ready()
    {
        return ($this->authorizedSupplier && $this->authorizedBuyer && $this->jwt);
    }


    /**
     *  Get a buyer supplier relationship based on authorized buyer supplier
     *  @return array
     */
    public function getBuyerSupplierRelationship()
    {
        $b = $this->requireBuyer();
        $s = $this->requireSupplier();
        return $this->get("v1/buyers/$b/suppliers/$s");
    }

    /**
     * Get terms choices
     */
    public function getTermsChoices()
    {
        $b = $this->requireBuyer();
        $s = $this->requireSupplier();
        return $this->get("v1/buyers/$b/suppliers/$s/terms-choices");
    }

    /**
     * Create the initial purchase order
     */
    public function createPurchaseOrder($data)
    {
        $b = $this->requireBuyer();
        $s = $this->requireSupplier();
        return $this->post("v1/buyers/$b/suppliers/$s/purchase-orders", $data);
    }

    /**
     * Confirm the checkout/payment agreement of a purchase order
     */
    public function confirmPurchaseOrder($hashid, $data)
    {
        return $this->post("v1/purchase-orders/$hashid/confirm", $data);
    }

    /**
     * Create the initial shipment
     */
    public function createShipment( $hashid, $data ) {
      return $this->post( "v1/purchase-orders/$hashid/shipments", $data );
    }

    private function requireSupplier()
    {
        if ( $this->authorizedSupplier )
            return $this->authorizedSupplier;
        else
            throw new Exception('Authorized Supplier is required');

    }
    private function requireBuyer()
    {
        if ( $this->authorizedBuyer )
            return $this->authorizedBuyer;
        else
            throw new Exception('Authorized Buyer is required');

    }

}
