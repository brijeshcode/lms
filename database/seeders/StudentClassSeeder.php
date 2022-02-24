<?php

namespace Database\Seeders;

use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use App\Models\Setup\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StudentClass::query()->truncate();
        Subject::query()->truncate();
        Chapter::query()->truncate();
        Topic::query()->truncate();

        $testData = [
            /*[
                'name' => '1st',
                'subjects' => [
                    [
                        'name' => 'Maths',
                        'chapters' => [
                            [
                                'name' => 'Chapter 1',
                                'topics' => [ 'Topic 1', 'Topic 2' ]
                            ],
                            [
                                'name' => 'Chapter 2',
                                'topics' => [ 'Topic 1', 'Topic 2' ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Science',
                        'chapters' => [
                            [
                                'name' => 'Chapter 1',
                                'topics' => [ 'Topic 1', 'Topic 2' ]
                            ],
                            [
                                'name' => 'Chapter 2',
                                'topics' => [ 'Topic 1', 'Topic 2' ]
                            ]
                        ]
                    ]
                ]
            ],
            ['name' => '2nd'],
            ['name' => '3rd'],
            ['name' => '4th'],
            ['name' => '5th'],*/
            ['name' => '6th'],
            [
                'name' => '7th',
                'subjects' => [
                    [
                        'name' => 'Maths',
                        'chapters' => [
                            [
                                'name' => 'Chapter 1: Integers',
                                'topics' => [ 'Properties of Addition and Subtraction of Integers', 'Multiplication of Integers', 'Properties of Multiplication of Integers', 'Division of Integers' ]
                            ],
                            [
                                'name' => 'Chapter 2: Fractions and Decimals',
                                'topics' => [ 'Topic 1', 'Topic 2' ]
                            ],
                            [
                                'name' => 'Chapter 3: Data Handling',
                                'topics' => [ 'Introduction to Data Handling', 'Collecting Data', 'Organisation of Data' ]
                            ],
                            [
                                'name' => 'Chapter 4: Simple Equations',
                                'topics' => [ 'A Mind-reading Game' ]
                            ],
                            [
                                'name' => 'Chapter 5: Lines and Angles',
                                'topics' => [ '5.1 Introduction to Lines and Angles' ]
                            ],
                            [
                                'name' => 'Chapter 6: The Triangle and its Properties',
                                'topics' => [ '6.1 Introduction to Triangles' ]
                            ],
                            [
                                'name' => 'Chapter 7: Congruence of Triangles',
                                'topics' => [ '7.1 Introduction', '7.2 Congruence of Plane Figures' ]
                            ],
                            [
                                'name' => 'Chapter 8: Comparing Quantities',
                                'topics' => [ '8.1 Introduction', '8.2 Equivalent Ratios' , '8.3 Percentage – Another way of Comparing Quantities' ]
                            ],
                            [
                                'name' => 'Chapter 9: Rational Numbers',
                                'topics' => [ '9.1 Introduction', '9.2 Need for Rational Numbers', '10.1 Introduction', '10.3 Construction of Triangles' ]
                            ],
                            [
                                'name' => 'Chapter 10: Practical Geometry',
                                'topics' => [ '10.1 Introduction' ]
                            ],
                            [
                                'name' => 'Chapter 11: Perimeter and Area',
                                'topics' => [ '11.1 Introduction to Area and Perimeter' ]
                            ],
                            [
                                'name' => 'Chapter 12: Algebraic Expressions',
                                'topics' => [ '12.1 Introduction', '12.2 How are Expressions Formed?', '12.3 Terms of an Expression' ]
                            ],
                            [
                                'name' => 'Chapter 13: Exponents and Powers',
                                'topics' => [ '13.1 Introduction', '13.2 Exponents', '13.3 Laws of Exponents']
                            ],
                            [
                                'name' => 'Chapter 14: Symmetry',
                                'topics' => [ '14.1 Introduction', '14.2 Lines of Symmetry for Regular polygons' ]
                            ],
                            [
                                'name' => 'Chapter 15: Visualising Solid Shapes',
                                'topics' => [ '15.1 Introduction: Plane Figures and Solid Shapes', '15.2 Faces, Edges and Vertices', '15.3 Nets for Building 3-D Shapes', '15.4 Drawing Solids on a Flat Surface', '15.5 Viewing Different Sections of a Solid' ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Science',
                        'chapters' => [
                            [
                                'name' => 'Chapter 1: Nutrition in Plants',
                                'topics' => [ 'Mode of nutrition in plants', 'food making process in plants' ]
                            ],
                            [
                                'name' => 'Chapter 2: Nutrition in Animals',
                                'topics' => [ 'Mode of nutrition in Animals 1', 'Food chain' ]
                            ]
                        ]
                    ]
                ]
            ],
            /*['name' => '8th'],
            ['name' => '9th'],
            ['name' => '10th'],
            ['name' => '11th'],
            ['name' => '12th']*/
        ];

        foreach ($testData as $key => $class) {
            $classId = StudentClass::create( ['name' => $class['name']] )->id;
            if (!isset($class['subjects']))continue;

            foreach ($class['subjects'] as $subject) {
                $subjectId = Subject::create(['class_id' => $classId, 'name' => $subject['name']])->id;
                if (!isset($subject['chapters']))continue;

                foreach ($subject['chapters'] as $key => $chapter) {
                    $chapterId = Chapter::create(['subject_id' => $subjectId, 'name' => $chapter['name']])->id;
                    if (!isset($chapter['topics']))continue;

                    foreach ($chapter['topics'] as $key => $topic) {
                        Topic::create(['chapter_id' => $chapterId, 'name' => $topic]);
                    }
                }
            }
            // code...
        }
    }
}
