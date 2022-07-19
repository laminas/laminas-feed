<?php

declare(strict_types=1);

namespace Laminas\Feed\PubSubHubbub\Model;

interface SubscriptionPersistenceInterface
{
    /**
     * Save subscription to RDMBS
     *
     * @param  array $data The key must be stored here as a $data['id'] entry
     * @return bool
     */
    public function setSubscription(array $data);

    /**
     * Get subscription by ID/key
     *
     * @param  string $key
     * @return array
     */
    public function getSubscription($key);

    /**
     * Determine if a subscription matching the key exists
     *
     * @param  string $key
     * @return bool
     */
    public function hasSubscription($key);

    /**
     * Delete a subscription
     *
     * @param  string $key
     * @return bool
     */
    public function deleteSubscription($key);
}
