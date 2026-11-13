<?php
/** Catálogo comercial y configuraciones privadas. No ejecuta agentes ni conecta con IA. */
class BusinessRepository
{
    public function __construct(private PDO $pdo) {}

    public function catalogue(string $query = '', string $category = ''): array
    {
        $sql = 'SELECT * FROM agents WHERE is_active=1';
        $parameters = [];
        if ($query !== '') { $sql .= ' AND (name LIKE ? OR summary LIKE ? OR purpose LIKE ?)'; array_push($parameters,'%'.$query.'%','%'.$query.'%','%'.$query.'%'); }
        if ($category !== '') { $sql .= ' AND category=?'; $parameters[]=$category; }
        $statement=$this->pdo->prepare($sql.' ORDER BY id');
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function findAgent(int $id): ?array
    {
        $statement=$this->pdo->prepare('SELECT * FROM agents WHERE id=? AND is_active=1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function configurations(int $userId): array
    {
        $statement=$this->pdo->prepare('SELECT c.*,(SELECT COUNT(*) FROM configuration_agents ca WHERE ca.config_id=c.id) AS agent_count FROM configurations c WHERE c.user_id=? ORDER BY c.updated_at DESC,c.id DESC');
        $statement->execute([$userId]);
        return $statement->fetchAll();
    }

    public function configuration(int $id, int $userId): ?array
    {
        $statement=$this->pdo->prepare('SELECT * FROM configurations WHERE id=? AND user_id=?');
        $statement->execute([$id,$userId]);
        $configuration=$statement->fetch();
        if (!$configuration) return null;
        $statement=$this->pdo->prepare('SELECT a.*,ca.position FROM configuration_agents ca JOIN agents a ON a.id=ca.agent_id WHERE ca.config_id=? ORDER BY ca.position,a.id');
        $statement->execute([$id]);
        $configuration['agents']=$statement->fetchAll();
        $configuration['agent_count']=count($configuration['agents']);
        return $configuration;
    }

    public function saveConfiguration(int $userId, array $data, array $agentIds, ?int $id = null): int
    {
        if (!$agentIds || count($agentIds)>20) throw new InvalidArgumentException('Selecciona entre uno y veinte agentes válidos.');
        $ids=[];
        foreach ($agentIds as $value) {
            $agentId=filter_var($value,FILTER_VALIDATE_INT);
            if (!$agentId || $agentId<1 || in_array($agentId,$ids,true)) throw new InvalidArgumentException('La selección de agentes contiene valores repetidos o no válidos.');
            $ids[]=$agentId;
        }
        $this->pdo->beginTransaction();
        try {
            if ($id !== null) {
                $statement=$this->pdo->prepare('SELECT id FROM configurations WHERE id=? AND user_id=? FOR UPDATE');
                $statement->execute([$id,$userId]);
                if (!$statement->fetch()) throw new InvalidArgumentException('No puedes editar esta configuración.');
            }
            // El IN solo se compone de marcadores; los ids siempre se enlazan como parámetros.
            $statement=$this->pdo->prepare('SELECT id FROM agents WHERE is_active=1 AND id IN ('.implode(',',array_fill(0,count($ids),'?')).')');
            $statement->execute($ids);
            if (count($statement->fetchAll())!==count($ids)) throw new InvalidArgumentException('Uno de los agentes ya no está disponible.');
            if ($id === null) {
                $statement=$this->pdo->prepare('INSERT INTO configurations (user_id,name,objective,sector,plan) VALUES (?,?,?,?,?)');
                $statement->execute([$userId,$data['name'],$data['objective'],$data['sector'],$data['plan']]);
                $id=(int)$this->pdo->lastInsertId();
            } else {
                $statement=$this->pdo->prepare('UPDATE configurations SET name=?,objective=?,sector=?,plan=?,updated_at=CURRENT_TIMESTAMP WHERE id=? AND user_id=?');
                $statement->execute([$data['name'],$data['objective'],$data['sector'],$data['plan'],$id,$userId]);
                $statement=$this->pdo->prepare('DELETE FROM configuration_agents WHERE config_id=?');
                $statement->execute([$id]);
            }
            $statement=$this->pdo->prepare('INSERT INTO configuration_agents (config_id,agent_id,position) VALUES (?,?,?)');
            foreach ($ids as $position=>$agentId) $statement->execute([$id,$agentId,$position+1]);
            $this->pdo->commit();
            return $id;
        } catch (Throwable $error) { $this->pdo->rollBack(); throw $error; }
    }

    public function deleteConfiguration(int $id, int $userId): void
    {
        $statement=$this->pdo->prepare('DELETE FROM configurations WHERE id=? AND user_id=?');
        $statement->execute([$id,$userId]);
    }

    public function createRequest(?int $userId, array $data): int
    {
        $rawId=$data['configuration_id'] ?? null;
        $configurationId=($rawId===null || $rawId==='') ? null : filter_var($rawId,FILTER_VALIDATE_INT);
        if ($configurationId !== null && (!$configurationId || $configurationId<1 || $userId===null || !$this->configuration($configurationId,$userId))) throw new InvalidArgumentException('La configuración seleccionada no pertenece a tu cuenta.');
        $statement=$this->pdo->prepare("INSERT INTO requests (user_id,configuration_id,company,contact_email,sector,objective,plan,message,status) VALUES (?,?,?,?,?,?,?,?,'nueva')");
        $statement->execute([$userId,$configurationId,$data['company'],$data['contact_email'],$data['sector'],$data['objective'],$data['plan'],$data['message']]);
        return (int)$this->pdo->lastInsertId();
    }

    public function requests(int $userId): array
    {
        $statement=$this->pdo->prepare('SELECT r.*,c.name AS configuration_name FROM requests r LEFT JOIN configurations c ON c.id=r.configuration_id WHERE r.user_id=? ORDER BY r.created_at DESC,r.id DESC');
        $statement->execute([$userId]);
        return $statement->fetchAll();
    }
}
