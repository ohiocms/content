<?php

use Belt\Core\Ability;
use Illuminate\Database\Seeder;

class BeltContentPermissbleSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ability::unguard();
        $abilities = [
            'blocks',
            'favorites',
            'handles',
            'pages',
            'posts',
        ];
        foreach ($abilities as $entity_type => $set) {
            if (is_numeric($entity_type)) {
                $entity_type = $set;
                $set = ['*', 'create', 'update', 'delete'];
            }
            $set = is_array($set) ? $set : [$set];
            foreach ($set as $ability) {
                Ability::firstOrCreate([
                    'entity_type' => $entity_type ?: null,
                    'name' => $ability,
                    'entity_id' => null,
                ]);
            }
        }
    }
}
