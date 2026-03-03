<?php

namespace App\Security;

use App\Entity\Participants;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Participants) {
            return;
        }

// C'est ici que la magie opère !
        if (!$user->isActif()) { // Vérifie le nom de ton getter (isActif ou getActif)
            throw new CustomUserMessageAccountStatusException('Votre compte est inactif. Veuillez contacter un administrateur.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
// On n'a rien besoin de faire après l'authentification ici
    }
}
