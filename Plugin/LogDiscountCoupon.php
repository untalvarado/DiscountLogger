<?php
/**
 * ESTRUTURA GENERADA POR IA 
 * Ajustado y personalizado por  Jorge Alvarado
 * Este archivo contiene la logica para interceptar el guardado del carrito.
 */

namespace Vendor\DiscountLogger\Plugin;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;
class LogDiscountCoupon
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     *@var string
     */
    protected $targetCouponCode;	
    /**
     * se realiza un registo en un log diferente al log estandar de Magento,
     * Se registra el hallazgo de un nuevo cupon en en custom_discount.log
     */

    public function __construct(LoggerInterface $logger,string $targetCouponCode)
    {
        $this->logger = $logger;
	$this->targetCouponCode= $targetCouponCode;
    }

    /**
     * Se intercepta el evento save del modelo Quote.
     *
     * @param Quote $subject El objeto del carrito
     * @param mixed $result El resultado de la operacion save original
     * @return mixed Se devuelve el resultado sin modificarlo
     */

    public function afterSave(Quote $subject, $result)    {

        try{
	        // Requerimiento estricto: Solo si el cupon es EXACTAMENTE "SAVE10"
        	$couponCode = $subject->getCouponCode();
		$foundCouponCode= $this->targetCouponCode;
        	if ($couponCode === $foundCouponCode) {
	            // Se calcula los montos requeridos
        	    $subtotal = $subject->getSubtotal();
	            $grandTotal = $subject->getGrandTotal();
        	    $discountAmount = $grandTotal - $subtotal; 
		    // Calculo simple del descuento
	            // Se prepara un mensaje con la informacion importante de la compra: fecha, totales y descuento
        	    // Nota:el timestamp es asignado automaticamente por Magento al log, sin embargo seinserta de nuevo por si hay problemas
	            $message = sprintf( '--- CUPON \'%s\' DETECTADO --- Fecha: %s | Subtotal: %s | GrandTotal: %s | Descuento: %s',
			$foundCouponCode,
			date('Y-m-d H:i:s'),
	                $subtotal,
        	        $grandTotal,
                	$discountAmount
	            );
        	    //La cadena generad se inserta en el log personalizado en var/log/custom_discount.log
	            $this->logger->info($message);
	        }
	    }catch (\Exception $e) { 
            //si se genera una excepcion se captura y se registra el mensaje
            $this->logger->error('Error en DiscountLogger: ' . $e->getMessage());
	    }
	    return $result;	
        
	}
}
