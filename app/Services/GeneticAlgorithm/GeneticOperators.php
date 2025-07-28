<?php

namespace App\Services\GeneticAlgorithm;

/**
 * Class GeneticOperators
 * Implementasi operasi genetika: seleksi, crossover, dan mutasi
 */
class GeneticOperators
{
    private float $crossoverRate;
    private float $mutationRate;
    private array $availableTimeSlots;
    private array $availableRooms;

    public function __construct(
        float $crossoverRate = 0.8,
        float $mutationRate = 0.1,
        array $availableTimeSlots = [],
        array $availableRooms = []
    ) {
        $this->crossoverRate = $crossoverRate;
        $this->mutationRate = $mutationRate;
        $this->availableTimeSlots = $availableTimeSlots;
        $this->availableRooms = $availableRooms;
    }

    /**
     * Seleksi tournament
     */
    public function tournamentSelection(array $population, int $tournamentSize = 3): Chromosome
    {
        $tournament = [];
        
        // Pilih individu secara acak untuk tournament
        for ($i = 0; $i < $tournamentSize; $i++) {
            $randomIndex = rand(0, count($population) - 1);
            $tournament[] = $population[$randomIndex];
        }
        
        // Pilih yang terbaik dari tournament
        usort($tournament, function ($a, $b) {
            return $b->getFitness() <=> $a->getFitness();
        });
        
        return $tournament[0];
    }

    /**
     * Seleksi roulette wheel
     */
    public function rouletteWheelSelection(array $population): Chromosome
    {
        $totalFitness = array_sum(array_map(function ($chromosome) {
            return $chromosome->getFitness();
        }, $population));
        
        if ($totalFitness == 0) {
            return $population[rand(0, count($population) - 1)];
        }
        
        $randomValue = mt_rand() / mt_getrandmax() * $totalFitness;
        $currentSum = 0;
        
        foreach ($population as $chromosome) {
            $currentSum += $chromosome->getFitness();
            if ($currentSum >= $randomValue) {
                return $chromosome;
            }
        }
        
        return $population[count($population) - 1];
    }

    /**
     * One-point crossover
     */
    public function onePointCrossover(Chromosome $parent1, Chromosome $parent2): array
    {
        if (mt_rand() / mt_getrandmax() > $this->crossoverRate) {
            return [$parent1->clone(), $parent2->clone()];
        }
        
        $genes1 = $parent1->getGenes();
        $genes2 = $parent2->getGenes();
        
        $minLength = min(count($genes1), count($genes2));
        if ($minLength <= 1) {
            return [$parent1->clone(), $parent2->clone()];
        }
        
        $crossoverPoint = rand(1, $minLength - 1);
        
        $offspring1Genes = array_merge(
            array_slice($genes1, 0, $crossoverPoint),
            array_slice($genes2, $crossoverPoint)
        );
        
        $offspring2Genes = array_merge(
            array_slice($genes2, 0, $crossoverPoint),
            array_slice($genes1, $crossoverPoint)
        );
        
        return [
            new Chromosome($offspring1Genes),
            new Chromosome($offspring2Genes)
        ];
    }

    /**
     * Two-point crossover
     */
    public function twoPointCrossover(Chromosome $parent1, Chromosome $parent2): array
    {
        if (mt_rand() / mt_getrandmax() > $this->crossoverRate) {
            return [$parent1->clone(), $parent2->clone()];
        }
        
        $genes1 = $parent1->getGenes();
        $genes2 = $parent2->getGenes();
        
        $minLength = min(count($genes1), count($genes2));
        if ($minLength <= 2) {
            return $this->onePointCrossover($parent1, $parent2);
        }
        
        $point1 = rand(1, $minLength - 2);
        $point2 = rand($point1 + 1, $minLength - 1);
        
        $offspring1Genes = array_merge(
            array_slice($genes1, 0, $point1),
            array_slice($genes2, $point1, $point2 - $point1),
            array_slice($genes1, $point2)
        );
        
        $offspring2Genes = array_merge(
            array_slice($genes2, 0, $point1),
            array_slice($genes1, $point1, $point2 - $point1),
            array_slice($genes2, $point2)
        );
        
        return [
            new Chromosome($offspring1Genes),
            new Chromosome($offspring2Genes)
        ];
    }

    /**
     * Uniform crossover
     */
    public function uniformCrossover(Chromosome $parent1, Chromosome $parent2): array
    {
        if (mt_rand() / mt_getrandmax() > $this->crossoverRate) {
            return [$parent1->clone(), $parent2->clone()];
        }
        
        $genes1 = $parent1->getGenes();
        $genes2 = $parent2->getGenes();
        
        $maxLength = max(count($genes1), count($genes2));
        $offspring1Genes = [];
        $offspring2Genes = [];
        
        for ($i = 0; $i < $maxLength; $i++) {
            if (mt_rand() / mt_getrandmax() < 0.5) {
                if (isset($genes1[$i])) $offspring1Genes[] = $genes1[$i];
                if (isset($genes2[$i])) $offspring2Genes[] = $genes2[$i];
            } else {
                if (isset($genes2[$i])) $offspring1Genes[] = $genes2[$i];
                if (isset($genes1[$i])) $offspring2Genes[] = $genes1[$i];
            }
        }
        
        return [
            new Chromosome($offspring1Genes),
            new Chromosome($offspring2Genes)
        ];
    }

    /**
     * Mutasi dengan mengubah waktu secara acak
     */
    public function timeMutation(Chromosome $chromosome): Chromosome
    {
        $mutatedChromosome = $chromosome->clone();
        $genes = $mutatedChromosome->getGenes();
        
        foreach ($genes as $index => $gene) {
            if (mt_rand() / mt_getrandmax() < $this->mutationRate) {
                // Mutasi waktu
                if (!empty($this->availableTimeSlots)) {
                    $randomTimeSlot = $this->availableTimeSlots[array_rand($this->availableTimeSlots)];
                    $gene['hari'] = $randomTimeSlot['hari'];
                    $gene['jam_mulai'] = $randomTimeSlot['jam_mulai'];
                    $gene['jam_selesai'] = $randomTimeSlot['jam_selesai'];
                }
                
                $mutatedChromosome->setGene($index, $gene);
            }
        }
        
        return $mutatedChromosome;
    }

    /**
     * Mutasi dengan mengubah ruangan secara acak
     */
    public function roomMutation(Chromosome $chromosome): Chromosome
    {
        $mutatedChromosome = $chromosome->clone();
        $genes = $mutatedChromosome->getGenes();
        
        foreach ($genes as $index => $gene) {
            if (mt_rand() / mt_getrandmax() < $this->mutationRate) {
                // Mutasi ruangan
                if (!empty($this->availableRooms)) {
                    $randomRoom = $this->availableRooms[array_rand($this->availableRooms)];
                    $gene['ruangan_id'] = $randomRoom['id'];
                }
                
                $mutatedChromosome->setGene($index, $gene);
            }
        }
        
        return $mutatedChromosome;
    }

    /**
     * Mutasi swap (menukar posisi dua gen)
     */
    public function swapMutation(Chromosome $chromosome): Chromosome
    {
        $mutatedChromosome = $chromosome->clone();
        $genes = $mutatedChromosome->getGenes();
        
        if (count($genes) < 2) {
            return $mutatedChromosome;
        }
        
        if (mt_rand() / mt_getrandmax() < $this->mutationRate) {
            $index1 = rand(0, count($genes) - 1);
            $index2 = rand(0, count($genes) - 1);
            
            // Swap genes
            $temp = $genes[$index1];
            $genes[$index1] = $genes[$index2];
            $genes[$index2] = $temp;
            
            $mutatedChromosome = new Chromosome($genes);
        }
        
        return $mutatedChromosome;
    }

    /**
     * Mutasi kombinasi (waktu + ruangan)
     */
    public function combinedMutation(Chromosome $chromosome): Chromosome
    {
        $chromosome = $this->timeMutation($chromosome);
        $chromosome = $this->roomMutation($chromosome);
        return $chromosome;
    }

    /**
     * Elitism selection - mempertahankan individu terbaik
     */
    public function elitismSelection(array $population, int $eliteCount): array
    {
        // Urutkan berdasarkan fitness (descending)
        usort($population, function ($a, $b) {
            return $b->getFitness() <=> $a->getFitness();
        });
        
        return array_slice($population, 0, $eliteCount);
    }

    /**
     * Set available time slots untuk mutasi
     */
    public function setAvailableTimeSlots(array $timeSlots): void
    {
        $this->availableTimeSlots = $timeSlots;
    }

    /**
     * Set available rooms untuk mutasi
     */
    public function setAvailableRooms(array $rooms): void
    {
        $this->availableRooms = $rooms;
    }

    /**
     * Get crossover rate
     */
    public function getCrossoverRate(): float
    {
        return $this->crossoverRate;
    }

    /**
     * Get mutation rate
     */
    public function getMutationRate(): float
    {
        return $this->mutationRate;
    }
}
