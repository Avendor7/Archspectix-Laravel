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
        return Inertia::render('Home', [
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
     * Normalizes results from ALR and AUR data sources into a single array.
     *
     * Assumes input arrays ($alrData, $aurData) have a 'results' key
     * containing an array of items (associative arrays or objects).
     *
     * @param array $alrData Associative array containing ALR results. Expected items keys: pkgname, pkgver, repo, description, last_update, flag_date.
     * @param array $aurData Associative array containing AUR results. Expected items keys: Name, Version, Description, LastModified (epoch), OutOfDate (epoch).
     * @return array An array of normalized result associative arrays.
     */
    function normalizeResults(array $alrData, array $aurData): array {
        $allResults = [];

        // Process ALR results (Arch Linux Repositories)
        // Use null coalescing operator (??) to safely access potentially missing keys
        if (isset($alrData['results']) && is_array($alrData['results'])) {
            foreach ($alrData['results'] as $result) {
                // Ensure $result is an array (if decoding JSON, it usually is)
                if (is_array($result)) {
                    $allResults[] = [
                        'name'              => $result['pkgname'] ?? null,
                        'version'           => $result['pkgver'] ?? null,
                        'repo'              => $result['repo'] ?? null,
                        'source'            => "ALR",
                        'description'       => $result['description'] ?? null,
                        'last_updated_date' => $result['last_update'] ?? null, // Assuming already ISO string or similar
                        'flagged_date'      => $result['flag_date'] ?? null    // Assuming already ISO string or similar
                    ];
                }
            }
        } else {
            // Optional: Log a warning if expected data structure is not found
            // error_log("Warning: 'results' key missing or not an array in alrData");
        }


        // Process AUR results (Arch User Repository)
        if (isset($aurData['results']) && is_array($aurData['results'])) {
            foreach ($aurData['results'] as $result) {
                if (is_array($result)) {
                    $allResults[] = [
                        'name'              => $result['Name'] ?? null,
                        'version'           => $result['Version'] ?? null,
                        'repo'              => "", // Repo is typically not applicable or empty for AUR in this context
                        'source'            => "AUR",
                        'description'       => $result['Description'] ?? null,
                        'last_updated_date' => $this->convertEpochToISO8601($result['LastModified'] ?? null),
                        'flagged_date'      => $this->convertEpochToISO8601($result['OutOfDate'] ?? null)
                    ];
                }
            }
        } else {
            // Optional: Log a warning if expected data structure is not found
            // error_log("Warning: 'results' key missing or not an array in aurData");
        }


        return $allResults;
    }
}
