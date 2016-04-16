<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');

		$this->command->info('User table seeded!');

		$this->call('NotesTableSeeder');

		$this->command->info('Notes table seeded and we done!');
	}
}

class UserTableSeeder extends Seeder {

  public function run() {
      // DB::table('users')->delete();
      // User::create(array('email' => 'test@test.com'));
  }

}

class NotesTableSeeder extends Seeder {
	public function run() {

			for($i = 0; $i < 365; $i++) {

				//define todays date
				//subtract $i from days

				$date_string = "".$i." days";
				$dateRaw = date("Y-m-d H:m:s");
				$dateTime = new DateTime($dateRaw);
				$date = date_sub($dateTime,date_interval_create_from_date_string($date_string))->format('Y-m-d H:m:s');
				// $date = "2010-01-01 15:36:41";
				$text = "Test Text for now";
				$random = intval(rand(0,99));

				for ($j = 0;$j < $random;$j++) {
					Note::create(array(
						'note' => $text,
						"created_at" => $date,
						"updated_at" => $date,
						"user_id" => 1)
					);
				}
			}
	}

}
