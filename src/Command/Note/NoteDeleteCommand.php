<?php
declare(strict_types=1);

namespace App\Command\Note;

class NoteDeleteCommand
{
    /**
     * @var int
     */
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

}