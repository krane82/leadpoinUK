<?php

print '<form method="POST" action="'. __HOST__ .'/docs/terms/download.php">';

print '<table class="table table-hover">

			<thead>

				<tr>

					<th>Terms and Conditions copy</th>

					<th>Docusign Information</th>

				</tr>

			</thead>

			<tbody>

				<tr>';

foreach ($data as $item)

{

    if ($item=='.' || $item=='..')continue;

    print '<td><button name="file" class="btn btn-sm btn-link" value="'.$item.'" target="a_blank" title="Download Current">'.$item.'</button></td>';

}

print '</tr></tbody></table>';

print '</form>';


