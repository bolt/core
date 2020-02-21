<?php

namespace Bolt\Utils;

use Bolt\Entity\User;

/**
 * This class is used to validate a user
 *
 * @author Ivo Valchev <ivo@twokings.nl>
 */
class UserValidator
{
    const DISPLAY_NAME_ERROR = 1;
    const PASSWORD_ERROR = 2;
    const EMAIL_ERROR = 3;

    /** @var string */
    private $password;

    /** @var string */
    private $displayName;

    /** @var string */
    private $email;

    /** @var array */
    private $validationErrors;

    public function __construct(User $user)
    {
        $this->password = $user->getPassword() ?? null;
        $this->displayName = $user->getDisplayName() ?? null;
        $this->email = $user->getEmail() ?? null;
        $this->validationErrors = [];
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function validate(): bool
    {
        $valid = true;

        $usernameValidateOptions = [
            'options' => [
                'min_range' => 1,
            ],
        ];

        // Validate username
        if ($this->displayName !== null && ! filter_var(mb_strlen(trim($this->displayName)), FILTER_VALIDATE_INT, $usernameValidateOptions)) {
            dump("here");
            $this->validationErrors[] = self::DISPLAY_NAME_ERROR;
            $valid = false;
        }

        // Validate email
        if ($this->email !== null && ! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = self::EMAIL_ERROR;
            $valid = false;
        }

        // Validate password
        if ($this->password !== null && mb_strlen($this->password) < 6) {
            $this->validationErrors[] = self::PASSWORD_ERROR;
            $valid = false;
        }

        return $valid;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function hasPasswordError(): bool
    {
        return in_array(UserValidator::PASSWORD_ERROR, $this->validationErrors);
    }

}
