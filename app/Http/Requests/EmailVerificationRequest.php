<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Auth\EmailVerificationRequest as BaseEmailVerificationRequest;

/**
 * La route de vérification est identifiée par le public_id opaque et non par la
 * clé primaire : on compare donc le public_id de l'utilisateur authentifié au
 * segment {id} du lien signé.
 */
class EmailVerificationRequest extends BaseEmailVerificationRequest
{
    public function authorize()
    {
        if (! hash_equals((string) $this->user()->public_id, (string) $this->route('id'))) {
            return false;
        }

        if (! hash_equals(sha1($this->user()->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }
}
