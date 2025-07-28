<?php

namespace App\Services\GeneticAlgorithm;

/**
 * Class Chromosome
 * Representasi kromosom untuk penjadwalan kelas
 * Setiap gen berisi informasi: kelas_id, mata_pelajaran_id, guru_id, ruangan_id, hari, jam_mulai, jam_selesai
 */
class Chromosome
{
    public array $genes = [];
    public float $fitness = 0.0;

    public function __construct(array $genes = [])
    {
        $this->genes = $genes;
    }

    /**
     * Menambahkan gen baru ke kromosom
     */
    public function addGene(array $gene): void
    {
        $this->genes[] = $gene;
    }

    /**
     * Mendapatkan gen berdasarkan index
     */
    public function getGene(int $index): ?array
    {
        return $this->genes[$index] ?? null;
    }

    /**
     * Mengatur gen pada index tertentu
     */
    public function setGene(int $index, array $gene): void
    {
        $this->genes[$index] = $gene;
    }

    /**
     * Mendapatkan jumlah gen dalam kromosom
     */
    public function getGeneCount(): int
    {
        return count($this->genes);
    }

    /**
     * Mendapatkan semua gen
     */
    public function getGenes(): array
    {
        return $this->genes;
    }

    /**
     * Mengatur fitness kromosom
     */
    public function setFitness(float $fitness): void
    {
        $this->fitness = $fitness;
    }

    /**
     * Mendapatkan fitness kromosom
     */
    public function getFitness(): float
    {
        return $this->fitness;
    }

    /**
     * Membuat salinan kromosom
     */
    public function clone(): self
    {
        return new self($this->genes);
    }

    /**
     * Mengacak urutan gen dalam kromosom
     */
    public function shuffle(): void
    {
        shuffle($this->genes);
    }

    /**
     * Mendapatkan gen berdasarkan kriteria tertentu
     */
    public function getGenesByDay(string $hari): array
    {
        return array_filter($this->genes, function ($gene) use ($hari) {
            return $gene['hari'] === $hari;
        });
    }

    /**
     * Mendapatkan gen berdasarkan kelas
     */
    public function getGenesByClass(int $kelasId): array
    {
        return array_filter($this->genes, function ($gene) use ($kelasId) {
            return $gene['kelas_id'] === $kelasId;
        });
    }

    /**
     * Mendapatkan gen berdasarkan guru
     */
    public function getGenesByTeacher(int $guruId): array
    {
        return array_filter($this->genes, function ($gene) use ($guruId) {
            return $gene['guru_id'] === $guruId;
        });
    }

    /**
     * Mendapatkan gen berdasarkan ruangan
     */
    public function getGenesByRoom(int $ruanganId): array
    {
        return array_filter($this->genes, function ($gene) use ($ruanganId) {
            return $gene['ruangan_id'] === $ruanganId;
        });
    }

    /**
     * Validasi apakah kromosom valid
     */
    public function isValid(): bool
    {
        foreach ($this->genes as $gene) {
            if (!isset($gene['kelas_id'], $gene['mata_pelajaran_id'], $gene['guru_id'], 
                       $gene['ruangan_id'], $gene['hari'], $gene['jam_mulai'], $gene['jam_selesai'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Konversi kromosom ke array untuk debugging
     */
    public function toArray(): array
    {
        return [
            'genes' => $this->genes,
            'fitness' => $this->fitness,
            'gene_count' => $this->getGeneCount()
        ];
    }
}
