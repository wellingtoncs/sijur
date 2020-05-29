select s.id_case as 'Codigo SISJUR', s.value as 'Codigo Legem', l.descricao as 'Atividade',
s.description as 'Comentarios', replace(vara, '- ', '') as 'Vara'  
from tb_sisjur s inner join tb_rob_eventos_sisjur sj 
on s.`type` = sj.codigo inner join tb_rob_eventos_legem l 
on sj.id_legem = l.id_legem
where robo_ins in (1, 4, 6) 
