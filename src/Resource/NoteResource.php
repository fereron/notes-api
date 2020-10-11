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
        $this->request           = $requestStack->getCurrentRequest();
        $this->imageCacheManager = $imageCacheManager;
    }

    /**
     * @param Note $note
     * @return array
     */
    public function toArray($note): array
    {
        return [
            'id'              => $note->getId(),
            'title'           => $note->getTitle(),
            'body'            => StringHelper::words($note->getBody(), 10),
            'created_at'      => $note->getCreatedAt()->format('Y-m-d H:i:s'),
            'image'           => $this->generateImageUrl($note->getImage()),
            'image_thumbnail' => $this->generateImageThumbnail($note->getImage())
        ];
    }

    private function generateImageUrl(Note\Image $image): ?string
    {
        if (!$image->getFilename()) {
            return null;
        }

        return $this->request->getUriForPath('/' . $image->getWebPath());
    }

    private function generateImageThumbnail(Note\Image $image): ?string
    {
        if (!$image->getFilename()) {
            return null;
        }

        return $this->imageCacheManager->getBrowserPath($image->getWebPath(), 'squared_thumbnail_small');
    }

}