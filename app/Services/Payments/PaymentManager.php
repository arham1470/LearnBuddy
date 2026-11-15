<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\Gateways\MockPaymentGateway;
use App\Services\Payments\Gateways\PayHereGateway;
use App\Services\Payments\Gateways\StripeGateway;
use InvalidArgumentException;

class PaymentManager
{
    /**
     * Instantiated gateway drivers.
     *
     * @var array<string, PaymentGatewayInterface>
     */
    protected array $gateways = [];

    /**
     * Get a payment gateway instance by name or default.
     */
    public function gateway(?string $name = null): PaymentGatewayInterface
    {
        $name = $name ?: (string) config('payments.default', 'payhere');

        if (! isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->resolve($name);
        }

        return $this->gateways[$name];
    }

    /**
     * Resolve the requested gateway implementation.
     */
    protected function resolve(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'stripe' => new StripeGateway(config('payments.gateways.stripe', [])),
            'payhere' => new PayHereGateway(config('payments.gateways.payhere', [])),
            'mock' => new MockPaymentGateway(config('payments.gateways.mock', [])),
            default => throw new InvalidArgumentException("Unsupported payment gateway [{$name}]."),
        };
    }
}
