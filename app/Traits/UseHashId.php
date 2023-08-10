<?php

declare(strict_types=1);

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait UseHashId
{
    public function getHashIdAttribute(): string
    {
        return $this->getHashId();
    }

    public function getHashId(): string
    {
        return Hashids::encode($this->{$this->getKeyName()});
    }

    public static function getIdFromHash(string $hashId): int
    {
        $idArray = Hashids::decode($hashId);

        if (! is_array($idArray)) {
            abort(404);
        }

        $id = reset($idArray);

        if ($id === false) {
            abort(404);
        }

        return $id;
    }

    /**
     * @param  bool  $httpResponse return an 404 HTTP erro rif model nor found?
     *
     * @throws ModelNotFoundException
     */
    public static function getModelFromHash(string $hashId, $httpResponse = true)
    {
        $id = self::getIdFromHash($hashId);
        $model = static::find($id);

        if ($model === null) {
            if ($httpResponse) {
                abort(404);

                return;
            }

            throw new ModelNotFoundException('No model for the Hash');
        }

        return $model;
    }
}
