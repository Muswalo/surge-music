<?php

namespace Muswalo\Surgemusic\Controllers;

use Muswalo\Surgemusic\Models\Music\Core\Song;
use Muswalo\Surgemusic\Utils\Response;

use Exception;

class SongController extends Song
{
    public function createSong(array $data): Response
    {
        try {
            $song = $this->create($data);
            return Response::success($song, "Song created successfully");
        } catch (Exception $e) {
            return Response::error("Failed to create song", ['error' => $e->getMessage()]);
        }
    }

    public function updateSong(int $id, array $data): Response
    {
        try {
            $existingSong = $this->find($id);
            if (!$existingSong) {
                return Response::error("Song not found");
            }
            $updated = $existingSong->update($id, $data);
            return Response::success($updated, "Song updated successfully");
        } catch (Exception $e) {
            return Response::error("Failed to update song", ['error' => $e->getMessage()]);
        }
    }

    public function deleteSong(int $id): Response
    {
        try {
            $existingSong = $this->find($id);
            if (!$existingSong) {
                return Response::error("Song not found");
            }
            $deleted = $existingSong->delete($id);
            return Response::success($deleted, "Song deleted successfully");
        } catch (Exception $e) {
            return Response::error("Failed to delete song", ['error' => $e->getMessage()]);
        }
    }

    public function getPaginated(int $perPage = 10, int $page = 1): Response
    {
        try {
            $paginatedData = $this->getAllPaginated($perPage, $page);
            return Response::success($paginatedData, "Paginated songs retrieved successfully");
        } catch (Exception $e) {
            return Response::error("Failed to get paginated songs", ['error' => $e->getMessage()]);
        }
    }

    public function getSongById(int $id): Response
    {
        try {
            $songData = $this->getById($id); 
            if (!$songData) {
                return Response::error("Song not found");
            }
            return Response::success($songData, "Song retrieved successfully");
        } catch (Exception $e) {
            return Response::error("Failed to get song by id", ['error' => $e->getMessage()]);
        }
    }
}
