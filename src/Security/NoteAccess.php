<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Note;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NoteAccess extends Voter
{
    public const MANAGE = 'manage';

    protected function supports(string $attribute, $subject)
    {
        return
            $attribute === self::MANAGE
            && $subject instanceof Note;
    }

    /**
     * @param string $attribute
     * @param Note $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user === $subject->getAuthor();
    }
}