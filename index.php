<?php
if (isset($_POST['start'])) {
    $grid = (int) $_POST['grid'];
    $players = (int) $_POST['players'];

    // Store data of players dice played.
    $players_data = [];

    // Build Grid
    $position = 1;
    $grid_data = [];
    $direction = 'right';
    for ($i = 0; $i < $grid; $i++) {
        if ($direction === 'right') {
            $grid_data[$i] = [];
            for ($j = 0; $j < $grid; $j++) {
                $grid_data[$i][$j] = $position;
                $position++;
            }
            $direction = 'left';
        } else {
            $grid_data[$i] = [];
            for ($j = ($grid - 1); $j >= 0; $j--) {
                $grid_data[$i][$j] = $position;
                $position++;
            }
            $direction = 'right';
        }
    }

    $someone_won = false;

    $max_positions = ($grid * $grid);

    while (!$someone_won) {
        for ($i = 1; $i <= $players; $i++) {
            $player = $i;
            if (!isset($players_data[$player])) {
                $players_data[$player] = [
                    'position' => [],
                    'dice_roll' => [],
                    'coordinates' => [],
                    'current_position' => 0,
                    'win_status' => false,
                ];
            }

            // Role the dice
            $dice_roll = rand(1, 6);

            // Set player position
            $players_data[$player]['dice_roll'][] = $dice_roll;

            if (($players_data[$player]['current_position'] + $dice_roll) > $max_positions) {
                $players_data[$player]['position'][] = $players_data[$player]['position'][count($players_data[$player]['position']) - 1];
                $players_data[$player]['coordinates'][] = $players_data[$player]['coordinates'][count($players_data[$player]['coordinates']) - 1];
                continue;
            }

            $players_data[$player]['current_position'] += $dice_roll;

            $current_grid_pos = 0;
            foreach ($grid_data as $grid_key => $grid_datum) {
                foreach ($grid_datum as $item_key => $item) {
                    $current_grid_pos++;
                    if ($current_grid_pos === $players_data[$player]['current_position']) {
                        $players_data[$player]['coordinates'][] = '('.$item_key.', '. $grid_key.')';
                        $players_data[$player]['position'][] = $item;
                        break 2;
                    }
                }
            }

            if ($players_data[$player]['current_position'] >= $max_positions) {
                $players_data[$player]['win_status'] = true;
                break 2;
            }
        }
    }


}

?>

<html>
<head>
    <title>Snake and Ladder - Dice Roll Game</title>
    <style>
        table {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-collapse: collapse;
        }

        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 3px 15px;
        }

        tr.black-border {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .text-center {
            text-align: center
        }
    </style>
</head>
<body>
<form method="post" action="" class="">
    <table class="">
        <thead>
        <tr>
            <th>GRID</th>
            <th><input type="text" name="grid" value=""/></th>
            <th></th>
            <th>Players</th>
            <th><input type="text" name="players" value=""/></th>
        </tr>
        </thead>
        <tbody style="border: 1px solid black">
        <tr>
            <td colspan="6" class="text-center">
                <div class="start-btn-div">
                    <button type="submit" name="start">START</button>
                </div>
            </td>
        </tr>
        <tr class="black-border">
            <td class="">Player No.</td>
            <td class="">Dice Roll History</td>
            <td class="">Position History</td>
            <td class="">Coordinate History</td>
            <td>Winner Status</td>
        </tr>
        <?php
        if (!empty($players_data)) {
            foreach ($players_data as $player_index => $players_datum) {
                ?>
                <tr class="black-border">
                    <td class=""><?php echo $player_index ?></td>
                    <td class="">
                        <?php
                        foreach ($players_datum['dice_roll'] as $dice_roll_history) {
                            echo $dice_roll_history . ', ';
                        }
                        ?>
                    </td>
                    <td class="">
                        <?php
                        foreach ($players_datum['position'] as $position_history) {
                            echo $position_history . ', ';
                        }
                        ?>
                    </td>
                    <td class="">
                        <?php
                        foreach ($players_datum['coordinates'] as $coordinate) {
                            echo $coordinate.', ';
                        }
                        ?>
                    </td>
                    <td><?php echo $players_datum['win_status'] ? 'WINNER' : '' ?></td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</form>
</body>
</html>
