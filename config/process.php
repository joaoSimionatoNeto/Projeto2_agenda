<?php
    session_start();
    include_once("connection.php");
    include_once("url.php");

    $data = $_POST;
    //modificações no banco

    if(!empty($data)){
        //criar contato
        if($data["type"] === "create"){
            $query = "INSERT INTO contacts (name, phone, observations)VALUES(:name, :phone, :observations);";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":phone", $data['phone']);
            $stmt->bindParam(":observations", $data['observations']);
            try {

               $stmt->execute();
               $_SESSION['msg'] = "Contato criado com sucesso!";
              
            } catch(PDOException $e) {
              
                // erro na conexão;
                $error = $e->getMessage();
                echo "Erro: $error";
                
            }
        }else if($data["type"] === "edit"){
      
            $query = "UPDATE contacts 
                      SET name = :name, phone = :phone, observations = :observations 
                      WHERE id = :id";
      
            $stmt = $conn->prepare($query);
      
            $stmt->bindParam(":name", $data["name"]);
            $stmt->bindParam(":phone", $data["phone"]);
            $stmt->bindParam(":observations", $data["observations"]);
            $stmt->bindParam(":id", $data["id"]);
      
            try {
      
              $stmt->execute();
              $_SESSION["msg"] = "Contato atualizado com sucesso!";
          
            } catch(PDOException $e) {
              // erro na conexão
              $error = $e->getMessage();
              echo "Erro: $error";
            }
        }else if($data["type"] === "delete"){
            $query = "DELETE  FROM contacts WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $data["id"]);
            try {
                $stmt->execute();
                $_SESSION["msg"] = "Contato removido com sucesso!";
            
              } catch(PDOException $e) {
                // erro na conexão
                $error = $e->getMessage();
                echo "Erro: $error";
              }
            }
    //redirecionamento para home
    header("location:$BASE_URL../index.php");
    //seleção de dados
    }else{
        $id;
        if(!empty($_GET)){
            $id = $_GET["id"];
        }
        //retorna o dado de um contato
        if(!empty($id)){
            $query = "SELECT * FROM contacts WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $contact = $stmt->fetch();
        }else{
            //retorna todos os contatos
            $contacts = [ ];
            $query = "SELECT * FROM contacts";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $contacts = $stmt->fetchAll();
        }
    }
    //fechar conexão 
    $conn = null;