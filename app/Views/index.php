<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swisshalley Test API</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
	<style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 2px;
			text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .loading {
            text-align: center;
            font-weight: bold;
        }

		.hotelImage {
			max-height: 50px;
		}

		fieldset {
			border: none;
			display: inline;
		}

		.nameCell, .imageCell {
			text-align: left;
		}

		li {
			display: inline;
			padding: 2px;
		}
    </style>
</head>
<body>
	<?php
		$session = session();
		$type = 'price';
		$direction = 'asc';
		$countryId = '';
		$cityId = '';
		if ($session->has('filter')) {
			$type = $session->get('filter')['type'] ?? 'price';
			$direction = $session->get('filter')['direction'] ?? 'asc';
			$countryId = $session->get('filter')['country_id'] ?? '';
			$cityId = $session->get('filter')['city_id'] ?? '';
		}
	?>	
	<h1>Hotelek</h1>
	<form method="post" action="/">
	<label for="country_id">Ország:</label><select name="country_id" id="country_id"><option></option></select>
	<label for="city_id">Város:</label><select name="city_id" id="city_id"><option></option></select>
	<fieldset id="type">
		<input type="radio" value="price" name="type" <?= $type == 'price' ? 'checked' : '' ?>>Ár</input>
		<input type="radio" value="star" name="type" <?= $type == 'star' ? 'checked' : '' ?>>Csillag</input>
	</fieldset>
	<fieldset id="direction">
		<input type="radio" value="asc" name="direction" <?= $direction == 'asc' ? 'checked' : '' ?>>Növekvő</input>
		<input type="radio" value="desc" name="direction" <?= $direction == 'desc' ? 'checked' : '' ?>>Csökkenő</input>
	</fieldset>
	<input type="submit" value="Szűrés">
	</form>
    <table id="dataTable">
        <thead>
            <tr>
                <th>Név</th>
                <th>Ország</th>
                <th>Város</th>
                <th>Ár</th>
                <th>Csillag</th>
                <th>Kép</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <tr class="loading">
                <td colspan="4">Adatok betöltése...</td>
            </tr>
        </tbody>
    </table>
	<div id="pager">

	</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const apiUrl = '/getHotels';
            const tableBody = document.getElementById('tableBody');
			const countryDropdown = document.getElementById('country_id');
			const cityDropdown = document.getElementById('city_id');

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';

					if (data.countries) {
						for (const [key, value] of Object.entries(data.countries)) {
							const option = document.createElement('option');
							option.value = key;
							option.textContent = value;
							countryDropdown.appendChild(option);
							if (key == '<?= $countryId ?>') {
								option.selected = true;
							}
						}
					}

					if (data.cities) {
						for (const [key, value] of Object.entries(data.cities)) {
							const option = document.createElement('option');
							option.value = key;
							option.textContent = value;
							cityDropdown.appendChild(option);
							if (key == '<?= $cityId ?>') {
								option.selected = true;
							}
						}
					}

                    if (data.hotels && data.hotels.length > 0) {
                        data.hotels.forEach(hotel => {
                            const row = document.createElement('tr');
							var star = "";
							for (i = 0; i < hotel.star; i++) {
								star += "&#9733;";
							}

                            row.innerHTML = `
                                <td  class="nameCell">${hotel.hotel_name}</td>
                                <td>${data.countries[hotel.country_id]}</td>
                                <td>${data.cities[hotel.city_id]}</td>
                                <td>${hotel.price} €</td>
								<td>${star}</td>
                                <td class="imageCell"><a href="${hotel.image}"><img class="hotelImage" src="${hotel.image}"/></a></td>
                            `;

                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="4">Nincsenek elérhető adatok</td>
                            </tr>
                        `;
                    }

					if (data.hotelsPager) {
						var pagerDiv = document.getElementById('pager');
						pager.innerHTML = data.hotelsPager;
					}
                })
                .catch(error => {
                    console.error('Hiba történt az adatok betöltésekor:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="4">Hiba történt az adatok betöltésekor</td>
                        </tr>
                    `;
                });
        });
    </script>
</body>
</html>
