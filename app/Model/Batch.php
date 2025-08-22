<?php 

function get_all_batches($conn){
	$sql = "SELECT * FROM batches ORDER BY start_date DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	if($stmt->rowCount() > 0){
		return $stmt->fetchAll();
	} else {
        return 0;
    }
}

function get_active_batches($conn){
	$sql = "SELECT * FROM batches WHERE status = 'active' ORDER BY batch_name ASC";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	if($stmt->rowCount() > 0){
		return $stmt->fetchAll();
	} else {
        return 0;
    }
}

function get_batch_by_id($conn, $id){
	$sql = "SELECT * FROM batches WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);
	if($stmt->rowCount() > 0){
		return $stmt->fetch();
	} else {
        return 0;
    }
}

// ## START: New Functions ##
function update_batch($conn, $data){
    $sql = "UPDATE batches SET batch_name=?, description=?, start_date=?, completion_date=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

function delete_batch($conn, $id){
    $sql = "DELETE FROM batches WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
}
// ## END: New Functions ##

function count_all_batches($conn){
	$sql = "SELECT id FROM batches";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);
	return $stmt->rowCount();
}

