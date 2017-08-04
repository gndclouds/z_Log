<?php
//creates timeline of given project/task through query ($q) with intervals at ($t)
function timeline($q) {
	$conn = connect();
	$result = $conn->query($q);

	if ($result->num_rows > 0) {
		$rows = array();

		//get query results
		while ($row = $result->fetch_assoc()) {
			array_push($rows, [$row['date'], $row['hours']]);
		}

		//get proper phrasing for hour and log numbers
		$first = new DateTime($rows[sizeof($rows)-1][0]);
		$last = new DateTime($rows[0][0]);
		$difference = $last->diff($first)->format("%a");
		$t = number_format((sizeof($rows) / 50), 2);
		if ($t < 1) $t = 1;

		//setup timeline layout
		echo
		'
		<div class="spacer" style="height: 15px;"></div>
		<span class="timeline-date-begin">'.$first->format('Y.m.d').'</span>
		<span class="timeline-date-end">'.$last->format('Y.m.d').'</span>
		<div class="timeline-container">
			<div class="timeline"></div>
			<div class="timeline-marker-begin"></div>
			<div class="timeline-circle-container">
		';

		//fill threshold (timeline node is filled if hours per day are higher than $threshold)
		$threshold = 3;

		//display dots
		if (sizeof($rows) > 1) {
			for ($i = sizeof($rows) - 1; $i > -1; $i-=$t) {
				$now = new DateTime($rows[$i][0]);
				$position = ($now->diff($first)->format("%a")) / $difference;

				$old = new DateTime($rows[$i - 1][0]);
				$oldPosition = ($old->diff($first)->format("%a")) / $difference;

				if ($now != $old && ($oldPosition - $position) > 0.001) {

					if ($rows[$i][1] >= $threshold) $fill = '#000';
					else $fill = '#fff';
					
					echo
					'
					<svg class="timeline-circle" style="left: '. $position * 100 .'%;">
						<circle cx="16" cy="16" r="7" stroke="#000" stroke-width="2.7" fill="'.$fill.'"/>
					</svg>
					';
				}
			}
		} else {
			echo
			'
			<svg class="timeline-circle" style="left: 0%;">
				<circle cx="16" cy="16" r="7" stroke="#000" stroke-width="2.7" fill="#000"/>
			</svg>
			';
		}

		//end timeline layout
		echo
		'
		</div>
		<svg class="timeline-marker-end">
			<live x1="0" y1="0" x2="0" y2="10" stroke-width="4"/>
		</svg>
		</div>
		';
	}
	$conn->close();
}
?>