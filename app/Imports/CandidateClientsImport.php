<?php

namespace App\Imports;

use App\Models\CandidateClient;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

class CandidateClientsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    use SkipsErrors;

    protected $imported = 0;
    protected $skipped = 0;
    protected $slug;

    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    public function model(array $row)
    {
        // Check if required fields exist
        if (!isset($row['name']) || !isset($row['email'])) {
            $this->skipped++;
            return null;
        }

        // Check for existing record
        $existing = CandidateClient::where('email', $row['email'])
            ->where('workspace', $this->slug)
            ->first();

        if ($existing) {
            $this->skipped++;
            return null;
        }

        $this->imported++;

        if (empty($row['name'])) {
            return null;
        }
        return new CandidateClient([
            'name' => $row['name'],
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'company_name' => $row['company_name'] ?? null,
            'workspace' => $this->slug
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'sometimes|nullable|email',
            'phone' => 'nullable',
            'company_name' => 'sometimes|nullable|string',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped + count($this->errors());
    }

    public function onError(Throwable $e)
    {
        $this->skipped++;
    }
}
