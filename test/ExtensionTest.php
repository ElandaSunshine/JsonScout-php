<?php
declare(strict_types=1);

use JsonScout\JsonScout;

require_once __DIR__ . '/TestBase.php';



//======================================================================================================================
class ExtensionTest
    extends TestBase
{
    #[\Override]
    public function setUp()
        : void
    {
        $this->testData = [
            // Example from: https://github.com/codebrainz/color-names/blob/master/output/colors.json
            "colours" => JsonScout::fromFile(__DIR__ . '/testdata/colours.json')
        ];

        $this->testCases = [
            // ======================= contains
            // find all green colours with "contains"
            '$[?contains(@.name, "Green")]' => [
                'data'   => 'colours',
                'expect' => JsonScout::fromFile(__DIR__ . '/testdata/only_green.json')
            ],

            // ======================= starts_with
            '$[?starts_with(@.name, "Raspberry")]' => [
                'data'   => 'colours',
                'expect' => [
                    (object) [
                        "name" => "Raspberry",
                        "hex" => "#e30b5d",
                        "rgb" => [227, 11, 93]
                    ],
                    (object) [
                        "name" => "Raspberry Glace",
                        "hex" => "#915f6d",
                        "rgb" => [145, 95, 109]
                    ],
                    (object) [
                        "name" => "Raspberry Pink",
                        "hex" => "#e25098",
                        "rgb" => [226, 80, 152]
                    ],
                    (object) [
                        "name" => "Raspberry Rose",
                        "hex" => "#b3446c",
                        "rgb" => [179, 68, 108]
                    ]
                ]
            ],

            // ======================= ends_with
            '$[?ends_with(@.name, "wood")]' => [
                'data'   => 'colours',
                'expect' => [
                    (object) [
                        "name" => "Burlywood",
                        "hex" => "#deb887",
                        "rgb" => [222, 184, 135]
                    ],
                    (object) [
                        "name" => "Redwood",
                        "hex" => "#ab4e52",
                        "rgb" => [171, 78, 82]
                    ],
                    (object) [
                        "name" => "Rosewood",
                        "hex" => "#65000b",
                        "rgb" => [101, 0, 11]
                    ]
                ]
            ],

            // ======================= to_lower & to_upper
            // find all that contain "wood" regardless case
            '$[?contains(to_lower(@.name), "wood")]' => [
                'data'   => 'colours',
                'expect' => [
                    (object) [
                        "name" => "Burlywood",
                        "hex" => "#deb887",
                        "rgb" => [222, 184, 135]
                    ],
                    (object) [
                        "name" => "Redwood",
                        "hex" => "#ab4e52",
                        "rgb" => [171, 78, 82]
                    ],
                    (object) [
                        "name" => "Rosewood",
                        "hex" => "#65000b",
                        "rgb" => [101, 0, 11]
                    ],
                    (object) [
                        "name" => "Dogwood Rose",
                        "hex" => "#d71868",
                        "rgb" => [215, 24, 104]
                    ],
                    (object) [
                        "name" => "Hollywood Cerise",
                        "hex" => "#f400a1",
                        "rgb" => [244, 0, 161]
                    ],
                    (object) [
                        "name" => "Wood Brown",
                        "hex" => "#c19a6b",
                        "rgb" => [193, 154, 107]
                    ]
                ],
                'order' => false
            ],
            '$[?contains(to_upper(@.name), "WOOD")]' => [
                'data'   => 'colours',
                'expect' => [
                    (object) [
                        "name" => "Burlywood",
                        "hex" => "#deb887",
                        "rgb" => [222, 184, 135]
                    ],
                    (object) [
                        "name" => "Redwood",
                        "hex" => "#ab4e52",
                        "rgb" => [171, 78, 82]
                    ],
                    (object) [
                        "name" => "Rosewood",
                        "hex" => "#65000b",
                        "rgb" => [101, 0, 11]
                    ],
                    (object) [
                        "name" => "Dogwood Rose",
                        "hex" => "#d71868",
                        "rgb" => [215, 24, 104]
                    ],
                    (object) [
                        "name" => "Hollywood Cerise",
                        "hex" => "#f400a1",
                        "rgb" => [244, 0, 161]
                    ],
                    (object) [
                        "name" => "Wood Brown",
                        "hex" => "#c19a6b",
                        "rgb" => [193, 154, 107]
                    ]
                ],
                'order' => false
            ],

            // should fail, no case conversion
            '$[?contains(@.name, "wood")]' => [
                'data'   => 'colours',
                'expect' => [
                    (object) [
                        "name" => "Burlywood",
                        "hex" => "#deb887",
                        "rgb" => [222, 184, 135]
                    ],
                    (object) [
                        "name" => "Redwood",
                        "hex" => "#ab4e52",
                        "rgb" => [171, 78, 82]
                    ],
                    (object) [
                        "name" => "Rosewood",
                        "hex" => "#65000b",
                        "rgb" => [101, 0, 11]
                    ],
                    (object) [
                        "name" => "Dogwood Rose",
                        "hex" => "#d71868",
                        "rgb" => [215, 24, 104]
                    ],
                    (object) [
                        "name" => "Hollywood Cerise",
                        "hex" => "#f400a1",
                        "rgb" => [244, 0, 161]
                    ],
                    (object) [
                        "name" => "Wood Brown",
                        "hex" => "#c19a6b",
                        "rgb" => [193, 154, 107]
                    ]
                ],
                'order' => false,
                'fail' => true
            ],

            // ======================= in
            // All that got any rgb value of 128 with "contains" (array)
            '$[?in(@.rgb, 128)]' => [
                'data'   => 'colours',
                'expect' => JsonScout::fromFile(__DIR__ . '/testdata/only_128.json')
            ],

            // ======================= typeof
            // All strings from the first 4 elements
            '$["air_force_blue_raf", "air_force_blue_usaf", "air_superiority_blue", "alabama_crimson"][?typeof(@) == "string"]' => [
                'data'   => 'colours',
                'expect' => [
                    "Air Force Blue (Raf)",
                    "#5d8aa8",
                    "Air Force Blue (Usaf)",
                    "#00308f",
                    "Air Superiority Blue",
                    "#72a0c1",
                    "Alabama Crimson",
                    "#a32638"
                ],
                "order" => false
            ],

            // All numbers from the first 4 elements
            '$["air_force_blue_raf", "air_force_blue_usaf", "air_superiority_blue", "alabama_crimson"]..[?typeof(@) == "number"]' => [
                'data'   => 'colours',
                'expect' => [
                    93, 138, 168,
                    0, 48, 143,
                    114, 160, 193,
                    163, 38, 56
                ],
                "order" => false
            ],
        ];
    }
}
