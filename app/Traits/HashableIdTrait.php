<?php
namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HashableIdTrait {
    public function __call($method, $arguments) {
        if (isset($this->hashableGetterFunctions) && isset($this->hashableGetterFunctions[$method])) {
            $value = $this->{$this->hashableGetterFunctions[$method]};
            return $value ? Hashids::encode($value) : $value;
        }

        if (isset($this->hashableGetterArrayFunctions) && isset($this->hashableGetterArrayFunctions[$method])) {
            $value = $this->{$this->hashableGetterArrayFunctions[$method]};
            if (count($value) > 0) {
                return array_map(fn($id) => Hashids::encode($id), $value);
            }
            return $value;
        }

        return parent::__call($method, $arguments);
    }

    public function getXIDAttribute() {
        return Hashids::encode($this->id);
    }
}
