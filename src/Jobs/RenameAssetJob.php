<?php

namespace DoeAnderson\StatamicCloudinary\Jobs;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use DoeAnderson\StatamicCloudinary\Helpers\CloudinaryHelper;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Statamic\Assets\Asset;

class RenameAssetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Asset
     */
    protected $asset;

    /**
     * @param Asset $asset
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    /**
     * @return int
     */
    public function retryAfter(): int
    {
        return 30;
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $oldPublicId = CloudinaryHelper::getPublicIdFromPath($this->asset->getOriginal('path'));
        $newPublicId = CloudinaryHelper::getPublicIdFromPath($this->asset->path());

        try {
            Cloudinary::rename(
                $oldPublicId,
                $newPublicId
            );
        } catch (Exception $e) {
            $message = "Cloudinary: {$e->getMessage()}";
            throw new Exception($message, 0, $e);
        }
    }
}
