<?php

namespace Corbpie\BunnyCdn;

class BunnyAPIStream extends BunnyAPI
{
    private int $stream_library_id;
    private string $stream_collection_guid;
    private string $stream_video_guid;

    //Stream library -> collection -> video
    public function setStreamLibraryId(int $library_id): void
    {
        $this->stream_library_id = $library_id;
    }

    public function setStreamCollectionGuid(string $collection_guid): void
    {
        $this->stream_collection_guid = $collection_guid;
    }

    public function setStreamVideoGuid(string $video_guid): void
    {
        $this->stream_video_guid = $video_guid;
    }

    public function getVideoCollections(int $page = 1, int $items_per_page = 100): array
    {
        return $this->APIcall('GET', "library/{$this->stream_library_id}/collections", ['page' => $page, 'itemsPerPage' => $items_per_page], 'STREAM');
    }

    public function getStreamCollections(int $page = 1, int $items_pp = 100, string $order_by = 'date'): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('GET', "library/{$this->stream_library_id}/collections?page=$page&itemsPerPage=$items_pp&orderBy=$order_by", [], 'STREAM');
    }

    public function getStreamForCollection(): array
    {
        $this->checkStreamLibraryIdSet();
        $this->checkStreamCollectionGuidSet();
        return $this->APIcall('GET', "library/{$this->stream_library_id}/collections/" . $this->stream_collection_guid, [], 'STREAM');
    }

    public function getStreamCollectionSize(): int
    {
        $this->checkStreamLibraryIdSet();
        $this->checkStreamCollectionGuidSet();
        return $this->APIcall('GET', "library/{$this->stream_library_id}/collections/" . $this->stream_collection_guid, [], 'STREAM')['totalSize'];
    }

    public function updateCollection(string $updated_collection_name): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('POST', "library/{$this->stream_library_id}/collections/" . $this->stream_collection_guid, array("name" => $updated_collection_name), 'STREAM');
    }

    public function deleteCollection(): array
    {
        $this->checkStreamLibraryIdSet();
        $this->checkStreamCollectionGuidSet();
        return $this->APIcall('DELETE', "library/{$this->stream_library_id}/collections/" . $this->stream_collection_guid, [], 'STREAM');
    }

    public function createCollection(string $new_collection_name): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('POST', "library/{$this->stream_library_id}/collections", array("name" => $new_collection_name), 'STREAM');
    }

    public function listVideos(int $page = 1, int $items_pp = 100, string $order_by = 'date'): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('GET', "library/{$this->stream_library_id}/videos?page=$page&itemsPerPage=$items_pp&orderBy=$order_by", [], 'STREAM');
    }

    public function listVideosForCollectionId(int $page = 1, int $items_pp = 100, string $order_by = 'date'): array
    {
        $this->checkStreamLibraryIdSet();
        $this->checkStreamCollectionGuidSet();
        return $this->APIcall('GET', "library/{$this->stream_library_id}/videos?collection={$this->stream_collection_guid}&page=$page&itemsPerPage=$items_pp&orderBy=$order_by", [], 'STREAM');
    }

    public function getVideoStatistics(): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('GET', "library/{$this->stream_library_id}/statistics", [], 'STREAM');
    }

    public function getVideoHeatmap(string $video_guid): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('GET', "library/{$this->stream_library_id}/videos/$video_guid/heatmap", [], 'STREAM');
    }

    public function getVideo(string $video_guid): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('GET', "library/{$this->stream_library_id}/videos/$video_guid", [], 'STREAM');
    }

    public function deleteVideo(string $video_guid): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('DELETE', "library/{$this->stream_library_id}/videos/$video_guid", [], 'STREAM');
    }

    public function createVideo(string $video_title): array
    {//Returns array containing a GUID which is needed to PUT the video file
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('POST', "library/{$this->stream_library_id}/videos", array("title" => $video_title), 'STREAM');
    }

    public function createVideoForCollection(string $video_title): array
    {//Returns array containing a GUID which is needed to PUT the video file
        $this->checkStreamLibraryIdSet();
        $this->checkStreamCollectionGuidSet();
        return $this->APIcall('POST', "library/{$this->stream_library_id}/videos", array("title" => $video_title, "collectionId" => $this->stream_collection_guid), 'STREAM');
    }

    public function uploadVideo(string $video_guid, string $video_to_upload): array
    {
        //Need to use createVideo() first to get video guid
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('PUT', "library/{$this->stream_library_id}/videos/" . $video_guid, array('file' => $video_to_upload), 'STREAM');

    }

    public function setThumbnail(string $video_guid, string $thumbnail_url): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('POST', "library/{$this->stream_library_id}/videos/$video_guid/thumbnail?$thumbnail_url", [], 'STREAM');

    }

    public function addCaptions(string $video_guid, string $srclang, string $label, string $captions_file): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('POST', "library/{$this->stream_library_id}/videos/$video_guid/captions/$srclang?label=$label&captionsFile=$captions_file", [], 'STREAM');
    }

    public function reEncodeVideo(string $video_guid): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('POST', "library/{$this->stream_library_id}/videos/$video_guid/reencode", [], 'STREAM');

    }

    public function fetchVideo(string $video_url, string $collection_id = null): array
    {//Downloads a video from a URL into stream library/collection
        $this->checkStreamLibraryIdSet();
        (!is_null($collection_id)) ? $append = "?collectionId=$collection_id" : $append = "";
        return $this->APIcall('POST', "library/{$this->stream_library_id}/videos/fetch{$append}", ['url' => $video_url], 'STREAM');

    }

    public function deleteCaptions(string $video_guid, string $srclang): array
    {
        $this->checkStreamLibraryIdSet();
        return $this->APIcall('DELETE', "library/{$this->stream_library_id}/videos/$video_guid/captions/$srclang", [], 'STREAM');
    }

    public function videoResolutionsArray(string $video_guid): array
    {
        $this->checkStreamLibraryIdSet();
        $data = $this->APIcall('GET', "library/{$this->stream_library_id}/videos/$video_guid", [], 'STREAM');
        return explode(",", $data['availableResolutions']);
    }

    public function videoSize(string $video_guid, string $size_type = 'MB', bool $format = false, float $decimals = 2): float
    {
        $this->checkStreamLibraryIdSet();
        $data = $this->APIcall('GET', "library/{$this->stream_library_id}/videos/$video_guid", [], false, true);
        return $this->convertBytes($data['storageSize'], $size_type, $format, $decimals);
    }

    private function checkStreamLibraryIdSet(): void
    {
        try {
            if (!isset($this->stream_library_id)) {
                throw new BunnyAPIException("You must set the stream library id first. Use setStreamLibraryId()");
            }
        } catch (BunnyAPIException $e) {//display error message
            echo $e->errorMessage();
            exit;
        }
    }

    private function checkStreamCollectionGuidSet(): void
    {
        try {
            if (!isset($this->stream_collection_guid)) {
                throw new BunnyAPIException("You must set the stream collection guid first. Use setStreamCollectionGuid()");
            }
        } catch (BunnyAPIException $e) {//display error message
            echo $e->errorMessage();
            exit;
        }
    }
}