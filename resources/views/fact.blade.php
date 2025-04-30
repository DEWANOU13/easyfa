<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>TDS-store</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">


  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  <!-- Favicon -->
  <link href="img/favicon.ico" rel="icon">

  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>
<style>
  body {
    font-size: 10px;
  }

  @media print {

    @page {
      size: A4 portrait;
      /* -moz-transform: rotate(-90deg) scale(.58, .58) */
    }

    print-area * {
      /* can be whatever CSS selector you need */
      transform: scale(100)
    }

    body {
      /* zoom: 60%; */
      /* transform-origin: 0px 0px; */
      /* transform: scale(1); */
      margin: 0px;
      /* width: 1400px; */
      /* -webkit-transform: scale(0.85); */
      /* Saf3.1+, Chrome */
      /* -moz-transform: scale(0.85); */
      /* FF3.5+ */
      /* -ms-transform: scale(0.85); */
      /* IE9 */
      /* -o-transform: scale(0.85); */
      /* Opera 10.5+ */
      /* transform: scale(0.85); */
    }

    #facture {
      contain: size;
    }
  }

</style>

<body>
  <div class="container-fluid col-md-8 offset-md-2">
    <div class="row">
      <div class="col-12">
        <div class="card my-5" id="facture">
          <div class="card-body p-0" style="">
            <div class="col-md-12 bg-danger entete" style="height: 200px"></div>
            <div class="col-md-12 px-md-4">
              <h3 class="text-center" style="">FACTURE PROFORMA</h3>
              <div class="row">
                <div class="col-md-6">
                  <div class="card border">
                    <div class="card-header pb-0" style="{{ background_color_3() }}">
                      <h5>AGENCE</h5>
                    </div>
                    <div class="card-body p-2">
                      <table>
                        <tbody>
                          <tr>
                            <td class="fw-bold">Agence :</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Date :</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Numero facture :</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Id vendeur :</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Vendeur :</td>
                            <td></td>
                          </tr>

                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card border">
                    <div class="card-header pb-0" style="{{ background_color_3() }}">
                      <h5>CLIENT</h5>
                    </div>
                    <div class="card-body p-2">
                      <table>
                        <tbody>
                          <tr>
                            <td class="fw-bold">Code :</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Client :</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Téléphone :</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td class="fw-bold">N° IFU :</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Adresse :</td>
                            <td></td>
                          </tr>

                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <br>
                <div class="col-12 my-3">
                  <div class="card border">
                    <div class="card-header pb-0" style="{{ background_color_3() }}">
                      <h5>INFO</h5>
                    </div>
                    <div class="card-body p-2 pb-0">
                      <table class="table table-bordered mb-0">
                        <tbody>
                          <tr>
                            <td style="width: 20%">Validité</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>Commentaire</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>Autres</td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>Référence facture d'origine</td>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <br>
                  <span class="fw-bold mb-3">Objet</span> : XXX
                  <br>
                  <table class="table table-bordered table-condensed mt-3">
                    <thead>
                      <tr>
                        <th>REF </th>
                        <th>DESIGNATION </th>
                        <th>QTE</th>
                        <th>PU TTC</th>
                        <th>MONTANT TTC</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                      </tr>
                      <tr>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                      </tr>
                      <tr>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                      </tr>
                      <tr>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                      </tr>
                      <tr>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                        <td>xxxxx</td>
                      </tr>
                      <tr>
                        <td colspan="3" class="text-right fw-bold" style="font-weight: bold">Total</td>
                        <td>xxx</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-8">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Groupe</th>
                        <th>Total</th>
                        <th>Imposable</th>
                        <th>Impôt</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>xxx</td>
                        <td>xxx</td>
                        <td>xxx</td>
                        <td>xxx</td>
                      </tr>
                      <tr>
                        <td>xxx</td>
                        <td>xxx</td>
                        <td>xxx</td>
                        <td>xxx</td>
                      </tr>
                      <tr>
                        <td>xxx</td>
                        <td>xxx</td>
                        <td>xxx</td>
                        <td>xxx</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-4">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Type de paiement</th>
                        <th>Payé</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Total </td>
                        <td>xxx</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-12">
                  Arrêtée la présente facture à la somme de XXX francs CFA
                </div>
                <div class="col-md-12 float-start">
                  <span class="float-right"><i>SERVICE FACTURATION</i></span>
                  <br>
                  <span class="float-right">A JOHN</span>
                </div>
              </div>
            </div>
            <div class="col-md-12 fixed-bottom entete bg-danger" style="height: 20px">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row float-right">
      <div class="col-12 mb-5">
        {{-- <a href="{{ route('root_espace_client_paiement_index') }}"> --}}
        <a href="{{ back()->getTargetUrl() }}">
          <button class="btn mx-4" style="background-color: #007bff; border: #007bff; color: white;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour</button>
        </a>

        <button class="btn border text-white" onClick="imprimer('facture')" style="{{ background_color_1() }};">
          <i class="fa fa-print" aria-hidden="true" input type="button" value="Imprimer"> </i> Imprimer
        </button>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

  <script src=https://code.jquery.com/jquery-3.4.1.min.js></script>
  <script src=https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js></script>
  <script>
    function imprimer(divName) {
      var printContents = document.getElementById(divName).innerHTML;
      var originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
      window.location.reload();
    }

  </script>

</body>
</html>
