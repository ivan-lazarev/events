<?php

function filter_add_action_button(&$row, $params) {
	if (isset($row['update'])) {
		$row['update'] = '<a href="admin.php?page='.$params['module'].'/update/'.$row['id'].'" title="Изменить">
					  	<img class="close" src="css/icon/edit.png" width="24" height="24"></a>';
	}
	if (isset($row['delete'])) {
		$row['delete'] = '<a onclick="return confirm(\'Вы действительно хотите удалить запись:\n'.$row[$params['delete_name']].'\')"
							 href="admin.php?page='.$params['module'].'/delete/'.$row['id'].'" title="Удалить">
						  	<img class="close" src="css/icon/del.png" width="24" height="24"></a>';
	}
}


// function filter_time(&$row,$head) {
// 	for ($i=0;$i<count($head);$i++){
// 		if ($head[$i] == 'time_visit'){
// 			$id_time = $i;
// 			break;
// 		}
// 	}
// 	if ($row[$id_time] != '0000-00-00 00:00:00') {
// 		$row[$id_time] = date('d.m.y H:i:s', strtotime($row[$id_time]));
// 	}
// 	else {
// 		$row[$id_time] = ' ';
// 	}
// }

// function filter_type(&$row,$head) {
// 	for ($i=0;$i<count($head);$i++){
// 		if ($head[$i] == 'type'){
// 			$id_type = $i;
// 			break;
// 		}
// 	}
// 	if ($row[$id_type] == 1) {
// 		$row[$id_type] = 'Обычный пользователь';
// 	}
// 	elseif ($row[$id_type] == 2) {
// 		$row[$id_type] = 'Администратор';
// 	}
// }

// function filter_data(&$row,$head) {
	
// 	for ($i=0;$i<count($head);$i++){
// 		if ($head[$i] == 'time'){
// 			$id_time = $i;
// 		}
// 		if ($head[$i] == 'time_in'){
// 			$id_time_in = $i;
// 		}
// 	}
// 	if ($row[$id_time_in] != '') {
// 		$row[$id_time_in] = date('d.m.y H:i:s', strtotime($row[$id_time_in]));
// 	}
// 	if ($row[$id_time] != '') {
// 		$row[$id_time] = date('d.m.y H:i:s', strtotime($row[$id_time]));
// 	}
// }

