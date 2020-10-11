<?php
declare(strict_types=1);

namespace App\Resource;

use App\DependencyInjection\Helper\StringHelper;
use App\Entity\Note;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as ImageCacheManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class NoteResource extends Resource
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var ImageCacheManager
     */
    private $imageCacheManager;

    public function __construct(RequestStack $requestStack, ImageCacheManager $imageCacheManager)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->imageCacheManager = $imageCacheManager;
    }

    /**
     * @param Note $note
     * @return array
     */
    public function toArray($note): array
    {
        $imageUrl = $note->getImage()
            ? $this->request->getUriForPath('/' . $note->getImage()->getWebPath())
            : null;

        $imagePreview = $note->getImage()
            ? $this->imageCacheManager->getBrowserPath($note->getImage()->getWebPath(), 'squared_thumbnail_small')
            : null;

        return [
            'id' => $note->getId(),
            'title' => $note->getTitle(),
            'body' => StringHelper::words($note->getBody(), 3),
            'created_at' => $note->getCreatedAt()->format('Y-m-d H:i:s'),
            'image' => $imageUrl,
            'image_thumbnail' => $imagePreview
        ];
    }

}