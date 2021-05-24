<?php
	// Connect to database
	include("db_connect.php");
	$request_method = $_SERVER["REQUEST_METHOD"];

	function getProducts()
	{
		global $PDOobject;
		$query = "SELECT * FROM produit";

		$stmt=$PDOobject->prepare($query);
		$stmt->execute();
		$response=$stmt->fetchall(PDO::FETCH_ASSOC);

		echo json_encode($response, JSON_PRETTY_PRINT);
		
	}

	
	function getProduct($id=0)
	{
		global $PDOobject;
		$query = "SELECT * FROM produit";
		if($id != 0)
		{
			$query .= " WHERE id= :ID LIMIT 1";
		}
		$stmt=$PDOobject->prepare($query);
		$stmt->bindValue(":ID",$id,PDO::PARAM_INT);
		$stmt->execute();
		$response=$stmt->fetch(PDO::FETCH_ASSOC);

		echo json_encode($response, JSON_PRETTY_PRINT);
	}
	
	function AddProduct()
	{
		global $PDOobject;
		$name = $_POST["name"];
		$description = $_POST["description"];
		$price = $_POST["price"];
		$quantity = $_POST["quantity"];
		$created = date('Y-m-d H:i:s');
		$modified = date('Y-m-d H:i:s');
			$query="INSERT INTO produit(name, description, price, quantity, created, modified) 
					VALUES('".$name."', '".$description."', '".$price."', '".$quantity."', '".$created."','".$modified."')";

		if($PDOobject->exec($query))
		{
			$response= array(
				'status' => 1,
				'status_message' =>'Produit ajoute avec succes.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'ERREUR!: '.$PDOobject->errorInfo()[2]
			);
		}
		echo json_encode($response,JSON_PRETTY_PRINT);
	}
	
	function updateProduct($id)
	{
		global $PDOobject;
		$_PUT = array();
		parse_str(file_get_contents('php://input'), $_PUT);
		$name = $_PUT["name"];
		$description = $_PUT["description"];
		$price = $_PUT["price"];
		$quantity = $_PUT["quantity"];
		$created = 'NULL';
		$modified = date('Y-m-d H:i:s');
		$query="UPDATE produit SET name='".$name."', description='".$description."', price='".$price."', quantity='".$quantity."', modified='".$modified."' WHERE id=".$id;
		
		
		if($PDOobject->exec($query))
		{
			$response= array(
				'status' => 1,
				'status_message' =>'Produit mis a jour avec succes.'
			);
		}
		else
		{
		
			$response=array(
				'status' => 0,
				'status_message' =>'ERREUR!: '. $PDOobject->errorInfo()[2]
			);
			
		}
		
		echo json_encode($response,JSON_PRETTY_PRINT);
	}
	
	function deleteProduct($id)
	{
		global $PDOobject;
		$query = "DELETE FROM produit WHERE id=".$id;
		if($PDOobject->exec($query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Produit supprime avec succes.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'ERREUR!: '. $PDOobject->errorInfo()[2]
			);
		}
		echo json_encode($response, JSON_PRETTY_PRINT);
	}
	
	

	switch($request_method)
	{
		
		case 'GET':
			// Retrive Products
			if(!empty($_GET["id"]))
			{
				$id=intval($_GET["id"]);
				getProduct($id);
			}
			else
			{
				getProducts();
			}
			break;
		default:
			// Invalid Request Method
			header("HTTP/1.0 405 Method Not Allowed");
			break;
			
		case 'POST':
			// Add product
			AddProduct();
			break;
			
		case 'PUT':
			// Update product
			$id = intval($_GET["id"]);
			updateProduct($id);
			break;
			
		case 'DELETE':
			// Delete product
			$id = intval($_GET["id"]);
			deleteProduct($id);
			break;

	}
?>