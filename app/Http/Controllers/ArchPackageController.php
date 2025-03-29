<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ArchPackageController extends Controller
{
    public function index()
    {
        // Implementation needed
    }

    public function searchAUR(Request $request): JsonResponse
    {
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['error' => 'Value parameter is required'], 400);
        }

        try {
            $url = "https://aur.archlinux.org/rpc/v5/search/$value";
            $response = Http::get($url);
            return response()->json($response->json());
        } catch (ConnectException $error) {
            Log::error('Connection error', ['error' => $error->getMessage()]);
            return response()->json(['error' => 'Connection error: ' . $error->getMessage()], 503);
        } catch (Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * SearchComponent the official Arch Linux repository
     */
    public function searchALR(Request $request): JsonResponse
    {
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['error' => 'Value parameter is required'], 400);
        }

        try {
            $url = "https://archlinux.org/packages/search/json/?q=$value";
            $response = Http::get($url);
            return response()->json($response->json());
        } catch (ConnectException $error) {
            Log::error('Connection error', ['error' => $error->getMessage()]);
            return response()->json(['error' => 'Connection error: ' . $error->getMessage()], 503);
        } catch (Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * SearchComponent both AUR and ALR repositories and normalize results
     */
    public function searchAll(Request $request): Response
    {
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['error' => 'Value parameter is required'], 400);
        }

        try {
            $alrData = $this->fetchALRData($value);
        } catch (ConnectException $error) {
            Log::error('Connection error', ['error' => $error->getMessage()]);
            return response()->json(['error' => 'Connection error: ' . $error->getMessage()], 503);
        } catch (Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }

        try {
            $aurData = $this->fetchAURData($value);
        } catch (ConnectException $error) {
            Log::error('Connection error', ['error' => $error->getMessage()]);
            return response()->json(['error' => 'Connection error: ' . $error->getMessage()], 503);
        } catch (Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }

        Log::info("SearchComponent complete for: $value");
        //return response()->json();
        return Inertia::render('SearchResultsComponent', [
            'results' => $this->normalizeResults($alrData, $aurData)
        ]);
    }

    /**
     * Get package info from the ALR repository
     */
    public function getALRInfo(Request $request): JsonResponse
    {
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['error' => 'Value parameter is required'], 400);
        }

        try {
            $data = $this->fetchALRPackageInfo($value);
            return response()->json($data);
        } catch (ConnectException $error) {
            Log::error('Connection error', ['error' => $error->getMessage()]);
            return response()->json(['error' => 'Connection error: ' . $error->getMessage()], 503);
        } catch (Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Get package info from the AUR repository
     */
    public function getAURInfo(Request $request): JsonResponse
    {
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['error' => 'Value parameter is required'], 400);
        }

        try {
            $data = $this->fetchAURPackageInfo($value);

            if (isset($data['results'][0])) {
                $data['results'][0]['LastModified'] = $this->convertEpochToISO8601($data['results'][0]['LastModified']);
                $data['results'][0]['OutOfDate'] = $this->convertEpochToISO8601($data['results'][0]['OutOfDate']);
            }

            return response()->json($data);
        } catch (ConnectException $error) {
            Log::error('Connection error', ['error' => $error->getMessage()]);
            return response()->json(['error' => 'Connection error: ' . $error->getMessage()], 503);
        } catch (Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Fetch data from ALR repository
     * @throws ConnectionException
     */
    private function fetchALRData(string $value)
    {
        $url = "https://archlinux.org/packages/search/json/?q=$value";
        return Http::get($url)->json();
    }

    /**
     * Fetch data from AUR repository
     * @throws ConnectionException
     */
    private function fetchAURData(string $value)
    {
        $url = "https://aur.archlinux.org/rpc/v5/search/$value";
        return Http::get($url)->json();
    }

    /**
     * Fetch package info from ALR repository
     * @throws ConnectionException
     */
    private function fetchALRPackageInfo(string $value)
    {
        $url = "https://archlinux.org/packages/search/json/?name=$value";
        return Http::get($url)->json();
    }

    /**
     * Fetch package info from AUR repository
     * @throws ConnectionException
     */
    private function fetchAURPackageInfo(string $value)
    {
        $url = "https://aur.archlinux.org/rpc/v5/info/$value";
        return Http::get($url)->json();
    }

    /**
     * Convert epoch timestamp to ISO8601 format
     */
    private function convertEpochToISO8601($timestamp)
    {
        if (!$timestamp) {
            return null;
        }

        return date('c', $timestamp);
    }

    /**
     * Normalize results from both repositories
     */
    private function normalizeResults(array $alrData, array $aurData): array
    {
        // Implementation needed
        return [
            'alr' => $alrData['results'] ?? [],
            'aur' => $aurData['results'] ?? []
        ];
    }
}
