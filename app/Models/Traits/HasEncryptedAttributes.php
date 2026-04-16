<?php

namespace App\Models\Traits;

use App\Services\EncryptionService;

trait HasEncryptedAttributes
{
    /**
     * Get the encryption service instance.
     */
    protected function getEncryptionService(): EncryptionService
    {
        return app(EncryptionService::class);
    }

    /**
     * Get the list of attributes that should be encrypted.
     *
     * @return array
     */
    abstract protected function getEncryptedAttributes(): array;

    /**
     * Encrypt an attribute value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getEncryptedAttributes()) && !empty($value)) {
            $value = $this->getEncryptionService()->encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Decrypt an attribute value.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->getEncryptedAttributes()) && !empty($value)) {
            $value = $this->getEncryptionService()->decrypt($value);
        }

        return $value;
    }

    /**
     * Get the array representation with decrypted attributes.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        foreach ($this->getEncryptedAttributes() as $attribute) {
            if (isset($array[$attribute])) {
                $array[$attribute] = $this->getAttribute($attribute);
            }
        }

        return $array;
    }
}
