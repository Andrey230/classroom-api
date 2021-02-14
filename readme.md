<h1>Classroom API</h1>

<h3>Run application</h3>

<ul>
    <li>composer install</li>
    <li>edit .env file (database configuration...)</li>
    <li>php bin/console doctrine:database:create</li>
    <li>php bin/console make:migration</li>
    <li>php bin/console doctrine:migrations:migrate</li>
</ul>

<h3>API request</h3>

<table>
<thead>
<tr>
    <th>URL</th>
    <th>Method</th>
    <th>Body parameters</th>
    <th>Desc</th>
</tr>
</thead>
<tbody>
<tr>
    <td>{BASE_URL}/api/classrooms</td>
    <td>GET</td>
    <td>-</td>
    <td>List of all classrooms</td>
</tr>
<tr>
    <td>{BASE_URL}/api/classrooms/{id}</td>
    <td>GET</td>
    <td>-</td>
    <td>Get classroom by id</td>
</tr>
<tr>
    <td>{BASE_URL}/api/classrooms</td>
    <td>POST</td>
    <td>name, date, isActive</td>
    <td>Create classroom</td>
</tr>
<tr>
    <td>{BASE_URL}/api/classrooms/{id}</td>
    <td>PUT</td>
    <td>name, date, isActive</td>
    <td>Update classroom by id</td>
</tr>
<tr>
    <td>{BASE_URL}/api/classrooms/{id}</td>
    <td>DELETE</td>
    <td>-</td>
    <td>Delete classroom by id</td>
</tr>
<tr>
    <td>{BASE_URL}/api/classrooms/activate/{id}</td>
    <td>PUT</td>
    <td>isActive</td>
    <td>Enabled/disabled classroom</td>
</tr>
</tbody>
</table>