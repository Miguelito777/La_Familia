select PD.id_periodos_docentes, M.nombreMateria from Materias M inner join Periodos_docentes  PD  on M.idMateria = PD.Materias_idMateria and PD.Grados_idGrado = 1;

