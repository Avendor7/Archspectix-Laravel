<?php

namespace App\Http\Controllers;

class ArchPackageController extends Controller
{
    public function index()
    {

    }
    public function searchAUR(Request $request): JsonResponse
    {
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['error' => 'Value parameter is required'], 400);
        }

        try {
            $url = "https://aur.archlinux.org/rpc/v5/search/{$value}";
            $response = Http::get($url);
            return response()->json($response->json());
        } catch (\Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Search the official Arch Linux repository
     */
    public function searchALR(Request $request): JsonResponse
    {
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['error' => 'Value parameter is required'], 400);
        }

        try {
            $url = "https://archlinux.org/packages/search/json/?q={$value}";
            $response = Http::get($url);
            return response()->json($response->json());
        } catch (\Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Search both AUR and ALR repositories and normalize results
     */
    public function searchAll(Request $request): JsonResponse
    {
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['error' => 'Value parameter is required'], 400);
        }

        try {
            $alrData = $this->fetchALRData($value);
        } catch (\Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }

        try {
            $aurData = $this->fetchAURData($value);
        } catch (\Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }

        Log::info("Search complete for: " . $value);
        return response()->json($this->normalizeResults($alrData, $aurData), 200);
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
        } catch (\Exception $error) {
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
        } catch (\Exception $error) {
            Log::error('Error', ['error' => $error->getMessage()]);
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Fetch data from the ALR API
     */
    private function fetchALRData(string $value): array
    {
        $url = "https://archlinux.org/packages/search/json/?q={$value}";
        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception($response->status());
        }

        return $response->json();
    }

    /**
     * Fetch data from the AUR API
     */
    private function fetchAURData(string $value): array
    {
        $url = "https://aur.archlinux.org/rpc/v5/search/{$value}";
        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception($response->status());
        }

        return $response->json();
    }

    /**
     * Fetch package info from the AUR API
     */
    private function fetchAURPackageInfo(string $value): array
    {
        $url = "https://aur.archlinux.org/rpc/v5/info?arg={$value}";
        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception($response->status());
        }

        return $response->json();
    }

    /**
     * Fetch package info from the ALR API
     */
    private function fetchALRPackageInfo(string $value): array
    {
        $url = "https://archlinux.org/packages/{$value}/json/";
        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception($response->status());
        }

        return $response->json();
    }

    /**
     * Convert epoch timestamp to ISO8601 format
     */
    private function convertEpochToISO8601($epoch): ?string
    {
        if (!$epoch) {
            return null;
        }

        return date('c', $epoch);
    }

    /**
     * Normalize results from both repositories
     */
    private function normalizeResults(array $alrData, array $aurData): array
    {
        // Implementation would depend on the structure of $alrData and $aurData
        // This is a placeholder for the normalization logic that was in the original code
        return [
            'alr' => $alrData,
            'aur' => $aurData
        ];
    }

}
