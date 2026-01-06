<?php
namespace Vendor\DiscountLogger\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;

class LoyaltyPoints implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        // Se obtiene el ID del usuario actual desde el contexto de GraphQL
        $currentUserId = $context->getUserId();
        // Validacion: Si el ID es 0 o null, es un usuario invitado (Guest)

        if (!$currentUserId || $currentUserId === 0) {
            throw new GraphQlAuthorizationException(
                __('El cliente debe estar autenticado para ver sus puntos de lealtad.')
            );
        }

       // Logica Mock: Retornar CustomerID * 10

        return (int)$currentUserId * 10;

    }

}
