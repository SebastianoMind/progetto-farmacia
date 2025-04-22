<?php
require_once('config/db_connection.php');
include('config/get_page.php');

// scegli l'operazione crud da eseguire in base al valore del parametro di "action"
$action = $_GET['action'];
$id = ($_GET['id']) ? $_GET['id'] : null;



switch ($action) {
    case 'view':
        //Titolo pagina
        $title = "Dettaglio del Farmaco";
        // Recupero informazioni farmaco
        $farmaco_info = " SELECT  `codice_minsan`,
                         `denominazione`,
                         farmaci.codice_atc,
                         DATE_FORMAT(data_scadenza, '%d/%m/%Y') AS data_scadenza_format,
                         `prezzo`,
                         `descrizione`,
                         ditta.ragione_sociale,
                         id_ditta,
                         principio_attivo.id AS id_princ_att,
                         principio_attivo.principio_attivo
                         
                FROM  `farmaci`
                JOIN ditta ON farmaci.id_ditta = ditta.id
                JOIN principio_attivo ON farmaci.codice_atc = principio_attivo.codice_atc
                WHERE farmaci.id = $id";

        $farmaco = $conn->query($farmaco_info);
        // get all farmaci
        $get_farmaco = $farmaco->fetchAll(PDO::FETCH_ASSOC);

        // Recupero informazioni azienda
        break;

    // Cancellazione del farmaco (principio attivo??)
    case 'delete':

        $title = 'Delete';

        if (isset($id) && !empty($_POST['id'])) {


            $delete_info = "DELETE FROM farmaci WHERE id = :id";

            $delete = $conn->prepare($delete_info);
            $delete->bindParam(':id', $id, PDO::PARAM_INT);

            if ($delete->execute()) {
                echo 'Farmaco' . $id . ' eliminato correttamente';
                header('location: farmaci.php');
                exit();
            } else {
                echo "Qualcosa è andato storto";
            }
        } else {
            if (!isset($action)) {
                echo "Manca il parametro 'action' ";
            } elseif (!isset($id)) {
                echo "Manca il parametro 'id' ";
            }
        }

        unset($delete);
        unset($conn);
        break;
    // aupdate del farmaco
    case 'update':
        $title = 'Modifica Farmaco';

        $codice_atc = $data_scadenza = $prezzo = $ditte_id = $codice_atc = '';

        $codice_minsan_err = $data_scadenza_err = $prezzo_err = $ditte_err =  $codice_atc_err = '';
        // QUERY DITTA
        $ditte_info  = "SELECT id, ragione_sociale FROM ditta";
        $ditte_result = $conn->query($ditte_info);
        $ditte = $ditte_result->fetchAll(PDO::FETCH_ASSOC);

        // Query principio_attivo
        $principi_info  = "SELECT codice_atc, principio_attivo FROM principio_attivo";
        $principi_result = $conn->query($principi_info);
        $principi = $principi_result->fetchAll(PDO::FETCH_ASSOC);
        if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
            if (isset($id) && !empty($_POST['id_farmaco'])) {
                // get hidden id
                $id = $_POST['id_farmaco'];


                // validazione input del form dopo submit
                $input_codice_minsan = trim($_POST['minsan']);
                if (empty($input_codice_minsan)) {
                    $codice_minsan_err = "Codice Minsan obbligatorio.";
                } elseif (!ctype_digit($input_codice_minsan)) {
                    $codice_minsan_err = "Scrivi un Codice Minsan valido";
                } elseif (($input_codice_minsan == $_POST['minsan']) || ($input_codice_minsan != $_POST['minsan'])) {
                    $codice_minsan =  $input_codice_minsan;
                }

                $input_data_scadenza = ($_POST['data_scadenza']);
                if (empty($input_data_scadenza)) {
                    $data_scadenza_err = "Inserisci una data di scadenza.";
                } else {
                    $data_scadenza = $input_data_scadenza;
                }

                $input_prezzo = trim($_POST['prezzo']);
                if (empty($input_prezzo)) {
                    $prezzo_err = "Inserisci il Prezzo del prodotto.";
                } elseif (!ctype_digit($input_prezzo)) {
                    $prezzo_err = "Inserisci un valore numerico valido.";
                } else {
                    $prezzo = $input_prezzo;
                }

                $input_ditte = trim($_POST['ditta']);
                if (empty($input_ditte)) {
                    $ditte_err = "Seleziona una casa produttrice";
                } else {
                    $ditte_id = $input_ditte;
                }

                $input_codice_atc = trim($_POST['princio_attivo']);
                if (empty($input_codice_atc)) {
                    $codice_atc_err = "Seleziona una principio attivo";
                } else {
                    $codice_atc = $input_codice_atc;
                }


                // altri controlli riguardo la validazione degli input


                if (empty($codice_minsan_err) && empty($data_scadenza_err) && empty($prezzo_err) && empty($codice_atc_err) && empty($ditte_err)) {
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        // Query di update
                        $update_farmaco_info = "UPDATE farmaci AS f
                                        JOIN principio_attivo AS p ON f.codice_atc = p.codice_atc
                                        JOIN ditta AS d ON f.id_ditta = d.id
                                        SET 
                                        codice_minsan = :codice_minsan,
                                        data_scadenza = :data_scadenza,
                                        prezzo = :prezzo,
                                        f.id_ditta = :ditte_id,
                                        p.codice_atc = :codice_atc 
                                        WHERE f.id = :id";
                        if ($update_farmaco = $conn->prepare($update_farmaco_info)) {
                            $update_farmaco->bindParam(':codice_minsan', $param_cod_minsan, PDO::PARAM_INT);
                            $update_farmaco->bindParam(':data_scadenza', $param_data_scadenza, PDO::PARAM_STR);
                            $update_farmaco->bindParam(':prezzo', $param_prezzo, PDO::PARAM_STR);
                            $update_farmaco->bindParam(':ditte_id', $param_ditte, PDO::PARAM_INT);
                            $update_farmaco->bindParam(':codice_atc', $param_codice_atc, PDO::PARAM_STR);
                            $update_farmaco->bindParam(':id', $param_id, PDO::PARAM_INT);

                            //settare i parametri

                            $param_cod_minsan = $codice_minsan;
                            $param_data_scadenza = $data_scadenza;
                            $param_prezzo = $prezzo;
                            $param_ditte = $ditte_id;
                            $param_codice_atc = $codice_atc;
                            $param_id = $id;


                            // eseguiamo i parametri
                            if ($update_farmaco->execute()) {
                                $new_update_farmaco = "SELECT *, ditta.id, ditta.ragione_sociale, principio_attivo.principio_attivo
                                                    FROM farmaci AS f
                                                    JOIN ditta on f.id_ditta = ditta.id
                                                    JOIN principio_attivo ON f.codice_atc = principio_attivo.codice_atc
                                                    WHERE f.id = :id";
                                if ($new_update = $conn->prepare($new_update_farmaco)) {
                                    $new_update->bindParam(':id', $param_id);
                                    if ($new_update->execute()) {
                                        if ($new_update->rowCount() == 1) {
                                            $new_row = $new_update->fetchAll(PDO::FETCH_ASSOC);
                                            echo "Dati aggiornati correttamente";
                                        } else {
                                            // gestire pagina errore
                                        }
                                    }
                                }
                            } else {
                                echo "Qualcosa è andato storto";
                            }
                        }
                    }
                    unset($update_farmaco);
                }
                unset($conn);
            }
        } else {
            if (isset($id) && !empty(trim($id))) {
                // get hidden id
                $id;
                $farmaco_info = "SELECT *, ditta.ragione_sociale, principio_attivo.principio_attivo
                FROM farmaci AS f
                JOIN ditta on f.id_ditta = ditta.id
                JOIN principio_attivo ON f.codice_atc = principio_attivo.codice_atc
                WHERE f.id = :id";
                if ($farmaco = $conn->prepare($farmaco_info)) {
                    $farmaco->bindParam(':id', $param_id);

                    $param_id = $id;
                    if ($farmaco->execute()) {
                        if ($farmaco->rowCount() == 1) {
                            $row = $farmaco->fetchAll(PDO::FETCH_ASSOC);
                        } else {
                            // gestire pagina errore
                        }
                    } else {
                        echo "Qualcosa è andato storto";
                    }
                }
                unset($farmaco);
                unset($conn);
            }
        }


        break;
}



?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="d-flex h-100 text-center text-white bg-dark">
    <section class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <?php include('template/header.php'); ?>


        <main>
            <h1><?= $title; ?></h1>
            <?php
            if ($action == 'delete') {
            ?>
                <form method="post">
                    <div class="alert alert-danger">
                        <p>Vuoi eliminare il farmaco selezionato?</p>
                        <input type="hidden" name="id" value="<?= trim($id) ?>">
                        <input type="submit" value="Si" class="btn btn-danger">
                        <a href="farmaci.php" class="btn btn-primary ml-2">No</a>
                    </div>
                </form>
            <?php
            } elseif ($action == 'view') {
            ?>
                <h2><?= $get_farmaco['0']['denominazione']; ?></h2>
                <table class="table table-dark table-striped-columns">
                    <thead>
                        <tr>
                            <th>Cod. minsan</th>
                            <th>data scadenza</th>
                            <th>Prezzo</th>
                            <th>produttore</th>
                        </tr>
                    </thead>
                    <tbody>
                        <td><?= $get_farmaco[0]['codice_minsan']; ?></td>
                        <td><?= $get_farmaco[0]['data_scadenza_format']; ?></td>
                        <td><?= $get_farmaco[0]['prezzo'] ?></td>
                        <td><a href="azienda-produttrice.php?id=<?= $get_farmaco[0]['id_ditta']; ?>"><?= $get_farmaco[0]['ragione_sociale']; ?></a></td>
                        <td><a href="principio-attivo.php?id=<?= $get_farmaco[0]['id_princ_att']; ?>"><?= $get_farmaco[0]['principio_attivo']; ?></a></td>
                    </tbody>

                </table>
                <h3>Descrizione Farmaco</h2>
                    <span><?= $get_farmaco[0]['descrizione'] ?></span>
                <?php  } elseif ($action == 'update') {
                ?>
                    <h2 class="mb-5"><?= ($_SERVER['REQUEST_METHOD'] == 'POST') ? $new_row[0]['denominazione'] : $row[0]['denominazione']  ?></h2>
                    <form method="post">
                        <table class="table table-dark  table-borderless table-striped-columns ">
                            <thead>
                                <tr>
                                    <th>Cod. minsan</th>
                                    <td>
                                        <input class="form-control <?= (!empty($codice_minsan_err)) ? 'is-invalid' : '';  ?>" type="text" name="minsan" value="<?= ($_SERVER['REQUEST_METHOD'] == 'POST') ? $new_row[0]['codice_minsan'] : $row[0]['codice_minsan']  ?>">
                                        <span class="invalid-feedback"><?= $codice_minsan_err; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>data scadenza</th>
                                    <td><input class="form-control <?= (!empty($data_scadenza_err)) ? 'is-invalid' : '';  ?>" type="date" name="data_scadenza" value="<?= ($_SERVER['REQUEST_METHOD'] == 'POST') ? $new_row[0]['data_scadenza'] : $row[0]['data_scadenza']  ?>">
                                        <span class="invalid-feedback"><?= $data_scadenza_err; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Prezzo</th>
                                    <td><input class="form-control <?= (!empty($prezzo_err)) ? 'is-invalid' : '';  ?>" type="text" name="prezzo" value="<?= ($_SERVER['REQUEST_METHOD'] == 'POST') ? $new_row[0]['prezzo'] : $row[0]['prezzo'] ?>">
                                        <span class="invalid-feedback"><?= $prezzo_err; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>produttore</th>
                                    <td>
                                        <select name="ditta" class="form-control <?= (!empty($ditte_err)) ? 'is-invalid' : '';  ?>" id="">
                                            <option value="">-- Seleziona --</option>
                                            <?php

                                            foreach ($ditte as $ditta) { ?>
                                                <option
                                                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                                        echo 'selected';
                                                    } elseif ($row[0]['id_ditta'] == $ditta['id']) {
                                                        echo  'selected';
                                                    } else {
                                                        echo '';
                                                    } ?> value="<?= $ditta['id'] ?>"><?= $ditta['id'] . " - " . $ditta['ragione_sociale']; ?></option>

                                            <?php } ?>

                                        </select>
                                        <span class="invalid-feedback"><?= $ditte_err; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Principio Atttivo</th>
                                    <td>
                                        <select name="princio_attivo" class="form-control <?= (!empty($codice_atc_err)) ? 'is-invalid' : '';  ?>">
                                            <option value="">-- Seleziona --</option>

                                            <?php foreach ($principi as $principio) { ?>
                                                <option
                                                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                                        echo '';
                                                    } elseif ($row[0]['codice_atc'] == $principio['codice_atc']) {
                                                        echo  'selected';
                                                    } else {
                                                        echo '';
                                                    } ?> value="<?= $principio['codice_atc'] ?>"><?= $principio['codice_atc'] . " - " .  $principio['principio_attivo']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="invalid-feedback"><?= $codice_atc_err; ?></span>

                                    </td>
                                </tr>
                                <tr>
                                    <th>Descrizione Farmaco</th>
                                    <td><input type="text" size="50" name="descrizione" value="<?= ($_SERVER['REQUEST_METHOD'] == 'POST') ? $new_row[0]['descrizione'] : $row[0]['descrizione']  ?>"></td>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input name="id_farmaco" type="hidden" value="<?= trim($id); ?>"></td>
                                    <td><button class="btn btn-primary" type="submit">Update</button></td>
                                </tr>
                            </tbody>

                        </table>

                    </form>


                <?php } ?>
        </main>


        <?php include('template/footer.php'); ?>

    </section>


</body>

</html>